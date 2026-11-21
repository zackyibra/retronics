<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalVerification;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RentalVerificationController extends Controller
{
    /**
     * Tampilkan surat perjanjian penyewaan resmi (lengkap dengan pasal, meterai, dan tanda tangan).
     */
    public function viewAgreement(int $rentalId): View
    {
        $rental = Rental::with(['items.product.category', 'user', 'verification.verifier'])->findOrFail($rentalId);

        if (! Auth::user()->isAdmin() && $rental->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat surat perjanjian ini.');
        }

        $verification = $rental->verification;
        if (! $verification) {
            abort(404, 'Data verifikasi dan surat perjanjian belum dibuat untuk pesanan ini.');
        }

        return view('rentals.agreement', compact('rental', 'verification'));
    }

    /**
     * Unduh / Pratinjau berkas identitas secara aman dari penyimpanan privat.
     */
    public function viewDocument(Request $request, int $rentalId, string $type): BinaryFileResponse
    {
        $rental = Rental::with('verification')->findOrFail($rentalId);

        if (! Auth::user()->isAdmin() && $rental->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mengakses dokumen ini.');
        }

        $verification = $rental->verification;
        if (! $verification) {
            abort(404, 'Dokumen verifikasi tidak ditemukan.');
        }

        $filePath = match ($type) {
            'id_card' => $verification->id_card_file,
            default => null,
        };

        $disk = null;
        if ($filePath) {
            if (Storage::disk('local')->exists($filePath)) {
                $disk = 'local';
            } elseif (Storage::disk('public')->exists($filePath)) {
                $disk = 'public';
            }
        }

        if (! $disk) {
            abort(404, 'Berkas dokumen fisik tidak ditemukan di sistem penyimpanan server.');
        }

        $fullPath = Storage::disk($disk)->path($filePath);

        if ($request->boolean('download')) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);

            return response()->download($fullPath, 'KTP-'.$rental->rental_code.'.'.$ext);
        }

        return response()->file($fullPath);
    }

    /**
     * Admin: Perbarui status verifikasi dokumen (Setujui, Minta Revisi, Tolak).
     */
    public function updateStatus(Request $request, int $rentalId): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk memverifikasi dokumen.');
        }

        $rental = Rental::with('verification')->findOrFail($rentalId);
        $verification = $rental->verification;

        if (! $verification) {
            return back()->with('error', 'Pesanan ini belum memiliki data verifikasi dokumen.');
        }

        if (in_array($rental->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Status verifikasi dokumen tidak dapat diubah pada pesanan yang sudah selesai atau dibatalkan.');
        }

        $validated = $request->validate([
            'verification_status' => ['required', 'string', 'in:menunggu,disetujui,ditolak,perlu_revisi'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'verification_status.required' => 'Pilih status keputusan verifikasi dokumen.',
            'verification_status.in' => 'Status keputusan tidak valid.',
        ]);

        if (in_array($validated['verification_status'], ['perlu_revisi', 'ditolak']) && empty($validated['admin_notes'])) {
            return back()->with('error', 'Wajib mencantumkan catatan/alasan saat menolak atau meminta revisi dokumen pelanggan.');
        }

        DB::transaction(function () use ($verification, $rental, $validated): void {
            $verification->update([
                'verification_status' => $validated['verification_status'],
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            if ($validated['verification_status'] === RentalVerification::STATUS_DITOLAK) {
                if ($rental->promo_code) {
                    $promo = Promotion::lockForUpdate()->where('code', $rental->promo_code)->first();
                    if ($promo && $promo->quota_used > 0) {
                        $promo->decrement('quota_used');
                    }
                }
                $rental->update([
                    'status' => 'rejected',
                    'admin_notes' => $validated['admin_notes'],
                ]);
            }
        });

        $verification->load(['user', 'rental']);
        WhatsAppService::sendVerificationStatusNotification($verification);

        $statusLabel = $verification->status_label;

        return back()->with('success', "Status verifikasi dokumen berhasil diperbarui menjadi: {$statusLabel}.");
    }

    /**
     * Pelanggan: Unggah ulang dokumen yang diminta revisi oleh admin.
     */
    public function resubmit(Request $request, int $rentalId): RedirectResponse
    {
        $rental = Rental::with('verification')->findOrFail($rentalId);

        if ($rental->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui pesanan ini.');
        }

        $verification = $rental->verification;
        if (! $verification || ! $verification->needsRevision()) {
            return back()->with('error', 'Dokumen pesanan ini sedang tidak dalam status membutuhkan revisi.');
        }

        $validated = $request->validate([
            'id_card_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'id_card_file.required' => 'Foto KTP asli wajib diunggah untuk perbaikan verifikasi.',
            'id_card_file.mimes' => 'Format file KTP harus berupa JPG, JPEG, PNG, atau PDF.',
            'id_card_file.max' => 'Ukuran file KTP maksimal 4MB.',
        ]);

        $oldFile = $verification->id_card_file;
        if ($oldFile && Storage::disk('local')->exists($oldFile)) {
            Storage::disk('local')->delete($oldFile);
        }

        $newPath = $request->file('id_card_file')->store('private/verifications/ktp', 'local');

        $updates = [
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
            'verified_by' => null,
            'verified_at' => null,
            'id_card_file' => $newPath,
        ];

        if (! empty($validated['notes'])) {
            $updates['admin_notes'] = 'Catatan revisi penyewa: '.$validated['notes'];
        }

        $verification->update($updates);

        return back()->with('success', 'Dokumen KTP perbaikan berhasil dikirim ulang. Menunggu pemeriksaan kembali oleh admin Retronics.');
    }
}
