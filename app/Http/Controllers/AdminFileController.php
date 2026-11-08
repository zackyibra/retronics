<?php

namespace App\Http\Controllers;

use App\Models\AdminFileAuditLog;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminFileController extends Controller
{
    /**
     * Tampilkan audit log penghapusan berkas dashboard admin.
     */
    public function auditLogs(Request $request): View
    {
        $query = AdminFileAuditLog::with('admin')->latest('deleted_at');

        if ($type = $request->input('file_type')) {
            $query->where('file_type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('reference_code', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('admin', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        return view('admin.files.audit_logs', compact('auditLogs'));
    }

    /**
     * Hapus berkas bukti transfer pembayaran (menjaga data pembayaran tetap utuh).
     */
    public function deletePaymentProof(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::with('rental')->findOrFail($id);

        if (empty($payment->payment_proof)) {
            return back()->with('error', 'Pembayaran ini tidak memiliki berkas bukti transfer.');
        }

        $proofPath = $payment->payment_proof;
        $reason = $request->input('reason', 'Penghapusan berkas bukti transfer oleh admin');

        // Catat audit log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'payment_proof',
            filePath: $proofPath,
            relatedModelType: Payment::class,
            relatedModelId: $payment->id,
            referenceCode: 'Pembayaran '.$payment->payment_code.' (Rental: '.($payment->rental->rental_code ?? '-').')',
            reason: $reason,
            fileName: basename($proofPath)
        );

        // Hapus fisik file dari storage public jika tidak lagi digunakan oleh pembayaran atau rental lain
        $isReferencedElsewhere = Payment::where('id', '!=', $payment->id)->where('payment_proof', $proofPath)->exists()
            || Rental::where('id', '!=', $payment->rental_id)->where('payment_proof', $proofPath)->exists();

        if (! $isReferencedElsewhere && Storage::disk('public')->exists($proofPath)) {
            Storage::disk('public')->delete($proofPath);
        }

        // Hapus referensi dari database tanpa menghapus data pembayaran
        $payment->update(['payment_proof' => null]);

        if ($payment->rental && $payment->rental->payment_proof === $proofPath) {
            $payment->rental->update(['payment_proof' => null]);
        }

        return back()->with('success', 'Berkas bukti pembayaran berhasil dihapus. Data transaksi, nominal, dan status tetap tersimpan aman.');
    }

    /**
     * Ganti berkas bukti pembayaran dengan file baru.
     */
    public function replacePaymentProof(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::with('rental')->findOrFail($id);

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [
            'payment_proof.required' => 'Pilih file bukti pembayaran pengganti.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format file yang diperbolehkan: JPEG, PNG, JPG, WEBP.',
            'payment_proof.max' => 'Ukuran file maksimal 3 MB.',
        ]);

        // Hapus file lama jika ada dan tidak digunakan record lain
        if (! empty($payment->payment_proof)) {
            $oldProof = $payment->payment_proof;
            $isReferencedElsewhere = Payment::where('id', '!=', $payment->id)->where('payment_proof', $oldProof)->exists()
                || Rental::where('id', '!=', $payment->rental_id)->where('payment_proof', $oldProof)->exists();

            if (! $isReferencedElsewhere && Storage::disk('public')->exists($oldProof)) {
                Storage::disk('public')->delete($oldProof);
            }
        }

        $newPath = $request->file('payment_proof')->store('payments', 'public');
        $payment->update(['payment_proof' => $newPath]);

        if ($payment->rental) {
            $payment->rental->update(['payment_proof' => $newPath]);
        }

        return back()->with('success', 'Berkas bukti pembayaran berhasil diperbarui.');
    }

    /**
     * Unduh berkas bukti pembayaran.
     */
    public function downloadPaymentProof(int $id): BinaryFileResponse
    {
        $payment = Payment::findOrFail($id);

        if (empty($payment->payment_proof) || ! Storage::disk('public')->exists($payment->payment_proof)) {
            abort(404, 'Berkas bukti transfer tidak ditemukan di server.');
        }

        $extension = pathinfo($payment->payment_proof, PATHINFO_EXTENSION);
        $fileName = 'Bukti-Bayar-'.$payment->payment_code.'.'.$extension;

        return response()->download(Storage::disk('public')->path($payment->payment_proof), $fileName);
    }

    /**
     * Hapus foto produk (menjaga data produk, harga, dan stok tetap utuh).
     */
    public function deleteProductImage(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        if (empty($product->image)) {
            return back()->with('error', 'Barang ini tidak memiliki berkas foto.');
        }

        $imagePath = $product->image;
        $reason = $request->input('reason', 'Penghapusan foto barang oleh admin');

        // Catat audit log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'product_image',
            filePath: $imagePath,
            relatedModelType: Product::class,
            relatedModelId: $product->id,
            referenceCode: 'Barang: '.$product->name,
            reason: $reason,
            fileName: basename($imagePath)
        );

        // Hapus file dari storage public jika tidak digunakan oleh produk lain
        $isReferencedElsewhere = Product::where('id', '!=', $product->id)->where('image', $imagePath)->exists();
        if (! $isReferencedElsewhere && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // Set kolom image menjadi null tanpa menghapus data produk
        $product->update(['image' => null]);

        return back()->with('success', "Foto untuk barang '{$product->name}' berhasil dihapus. Data barang, harga, dan stok tetap utuh.");
    }

    /**
     * Hapus banner/gambar promo (menjaga data promo dan kode diskon tetap utuh).
     */
    public function deletePromotionImage(Request $request, int $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);

        if (empty($promotion->image)) {
            return back()->with('error', 'Promo ini tidak memiliki gambar banner.');
        }

        $imagePath = $promotion->image;
        $reason = $request->input('reason', 'Penghapusan banner promo oleh admin');

        // Catat audit log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'promotion_image',
            filePath: $imagePath,
            relatedModelType: Promotion::class,
            relatedModelId: $promotion->id,
            referenceCode: 'Promo: '.$promotion->code,
            reason: $reason,
            fileName: basename($imagePath)
        );

        $isReferencedElsewhere = Promotion::where('id', '!=', $promotion->id)->where('image', $imagePath)->exists();
        if (! $isReferencedElsewhere && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        $promotion->update(['image' => null]);

        return back()->with('success', "Gambar banner promo '{$promotion->code}' berhasil dihapus. Data diskon dan kode promo tetap aktif.");
    }

    /**
     * Hapus foto kerusakan / lampiran denda (menjaga data denda tetap utuh).
     */
    public function deleteFinePhoto(Request $request, int $id): RedirectResponse
    {
        $fine = Fine::with('rental')->findOrFail($id);

        if (empty($fine->photo)) {
            return back()->with('error', 'Catatan denda ini tidak memiliki foto lampiran.');
        }

        $photoPath = $fine->photo;
        $reason = $request->input('reason', 'Penghapusan lampiran foto denda oleh admin');

        // Catat audit log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'fine_photo',
            filePath: $photoPath,
            relatedModelType: Fine::class,
            relatedModelId: $fine->id,
            referenceCode: 'Denda #'.$fine->id.' (Rental: '.($fine->rental->rental_code ?? '-').')',
            reason: $reason,
            fileName: basename($photoPath)
        );

        $isReferencedElsewhere = Fine::where('id', '!=', $fine->id)->where('photo', $photoPath)->exists();
        if (! $isReferencedElsewhere && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }

        $fine->update(['photo' => null]);

        return back()->with('success', 'Foto lampiran denda berhasil dihapus. Riwayat denda dan nominal tetap tersimpan.');
    }

    /**
     * Hapus foto kondisi pengembalian barang.
     */
    public function deleteReturnPhoto(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::findOrFail($id);

        if (empty($rental->return_photo)) {
            return back()->with('error', 'Pesanan ini tidak memiliki foto pengembalian.');
        }

        $photoPath = $rental->return_photo;
        $reason = $request->input('reason', 'Penghapusan foto kondisi pengembalian oleh admin');

        // Catat audit log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'return_photo',
            filePath: $photoPath,
            relatedModelType: Rental::class,
            relatedModelId: $rental->id,
            referenceCode: 'Pengembalian '.$rental->rental_code,
            reason: $reason,
            fileName: basename($photoPath)
        );

        $isReferencedElsewhere = Rental::where('id', '!=', $rental->id)->where('return_photo', $photoPath)->exists();
        if (! $isReferencedElsewhere && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }

        $rental->update(['return_photo' => null]);

        return back()->with('success', 'Foto kondisi pengembalian berhasil dihapus tanpa mengubah data transaksi.');
    }

    /**
     * Hapus dokumen KTP/identitas penyewa dengan perlindungan ketat dan alasan wajib.
     */
    public function deleteVerificationDocument(Request $request, int $rentalId, string $type): RedirectResponse
    {
        $rental = Rental::with('verification')->findOrFail($rentalId);
        $verification = $rental->verification;

        if (! $verification) {
            return back()->with('error', 'Pesanan ini tidak memiliki data verifikasi dokumen.');
        }

        $filePath = match ($type) {
            'id_card' => $verification->id_card_file,
            default => null,
        };

        if (empty($filePath)) {
            return back()->with('error', 'Dokumen identitas tidak ditemukan atau sudah dihapus sebelumnya.');
        }

        // PERLINDUNGAN KETAT: Dokumen yang masih aktif digunakan dilarang dihapus sembarangan
        $activeRentalStatuses = ['pending', 'waiting_verification', 'active', 'in_use'];
        $isPendingVerification = in_array($verification->verification_status, ['menunggu', 'waiting_verification']);

        if (in_array($rental->status, $activeRentalStatuses) || $isPendingVerification) {
            return back()->with('error', 'Dokumen KTP ini terlindungi dan tidak dapat dihapus karena transaksi penyewaan atau proses verifikasi masih berlangsung aktif.');
        }

        // Validasi alasan penghapusan (wajib diisi oleh admin)
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Wajib mengisi alasan penghapusan dokumen identitas pelanggan.',
            'reason.min' => 'Alasan penghapusan dokumen minimal 5 karakter.',
        ]);

        // Catat ke Audit Log
        AdminFileAuditLog::logDeletion(
            adminId: Auth::id(),
            fileType: 'id_card_file',
            filePath: $filePath,
            relatedModelType: RentalVerification::class,
            relatedModelId: $verification->id,
            referenceCode: 'KTP Penyewa: '.$rental->rental_code.' ('.$verification->full_name.')',
            reason: $validated['reason'],
            fileName: basename($filePath)
        );

        // Hapus berkas fisik jika ada di local disk atau public disk dan tidak dirujuk data lain
        $isReferencedElsewhere = RentalVerification::where('id', '!=', $verification->id)->where('id_card_file', $filePath)->exists();
        if (! $isReferencedElsewhere) {
            if (Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            } elseif (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        // Hapus referensi file pada database tanpa menghapus data verifikasi
        $verification->update([
            'id_card_file' => null,
        ]);

        return back()->with('success', 'Berkas dokumen identitas KTP berhasil dihapus dan tindakan telah dicatat dalam audit log.');
    }

    /**
     * Ganti berkas dokumen KTP penyewa jika alur bisnis mengizinkan koreksi.
     */
    public function replaceVerificationDocument(Request $request, int $rentalId, string $type): RedirectResponse
    {
        $rental = Rental::with('verification')->findOrFail($rentalId);
        $verification = $rental->verification;

        if (! $verification) {
            return back()->with('error', 'Data verifikasi pesanan tidak ditemukan.');
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:4096'],
        ], [
            'document.required' => 'Pilih berkas dokumen pengganti.',
            'document.mimes' => 'Format berkas harus JPEG, PNG, JPG, atau PDF.',
            'document.max' => 'Ukuran berkas maksimal 4 MB.',
        ]);

        $oldPath = $verification->id_card_file;
        if (! empty($oldPath)) {
            $isReferencedElsewhere = RentalVerification::where('id', '!=', $verification->id)->where('id_card_file', $oldPath)->exists();
            if (! $isReferencedElsewhere) {
                if (Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                } elseif (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $newPath = $request->file('document')->store('verifications/id_cards', 'local');
        $verification->update([
            'id_card_file' => $newPath,
        ]);

        return back()->with('success', 'Dokumen identitas berhasil diperbarui.');
    }
}
