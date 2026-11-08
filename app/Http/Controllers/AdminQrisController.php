<?php

namespace App\Http\Controllers;

use App\Models\AdminFileAuditLog;
use App\Models\QrisSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminQrisController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan kode QRIS pembayaran toko.
     */
    public function index(): View
    {
        $activeQris = QrisSetting::getActive();
        $qrisList = QrisSetting::with('uploader')
            ->latest()
            ->paginate(10);

        return view('admin.qris.index', compact('activeQris', 'qrisList'));
    }

    /**
     * Unggah dan simpan konfigurasi QRIS baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_name' => ['required', 'string', 'max:100'],
            'nmid' => ['required', 'string', 'max:100'],
            'account_notes' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'merchant_name.required' => 'Nama merchant QRIS wajib diisi.',
            'nmid.required' => 'National Merchant ID (NMID) wajib diisi.',
            'image.required' => 'File gambar kode QRIS wajib diunggah.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 3 MB.',
        ]);

        $path = $request->file('image')->store('qris', 'public');
        $isActive = $request->boolean('is_active', true);

        if ($isActive) {
            QrisSetting::where('is_active', true)->update(['is_active' => false]);
        }

        QrisSetting::create([
            'merchant_name' => $validated['merchant_name'],
            'nmid' => $validated['nmid'],
            'account_notes' => $validated['account_notes'] ?? null,
            'image_path' => $path,
            'is_active' => $isActive,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.qris.index')
            ->with('success', 'Kode QRIS resmi berhasil diunggah dan disimpan!');
    }

    /**
     * Aktifkan atau nonaktifkan status penayangan QRIS.
     */
    public function toggle(int $id): RedirectResponse
    {
        $qris = QrisSetting::findOrFail($id);

        if (! $qris->is_active) {
            // Aktifkan QRIS ini dan nonaktifkan yang lain
            QrisSetting::where('id', '!=', $id)->update(['is_active' => false]);
            $qris->update(['is_active' => true]);
            $message = 'QRIS '.$qris->merchant_name.' telah diaktifkan untuk pembayaran pelanggan.';
        } else {
            $qris->update(['is_active' => false]);
            $message = 'QRIS '.$qris->merchant_name.' telah dinonaktifkan.';
        }

        return redirect()->route('admin.qris.index')
            ->with('success', $message);
    }

    /**
     * Hapus konfigurasi dan file QRIS (hanya untuk QRIS non-aktif / arsip).
     */
    public function destroy(int $id): RedirectResponse
    {
        $qris = QrisSetting::findOrFail($id);

        if ($qris->is_active) {
            return redirect()->route('admin.qris.index')
                ->with('error', 'QRIS yang sedang aktif digunakan tidak dapat dihapus. Silakan aktifkan QRIS lain terlebih dahulu sebelum menghapus berkas ini.');
        }

        $imagePath = $qris->image_path;
        if ($imagePath) {
            // Catat audit log penghapusan file
            AdminFileAuditLog::logDeletion(
                adminId: Auth::id(),
                fileType: 'qris_image',
                filePath: $imagePath,
                relatedModelType: QrisSetting::class,
                relatedModelId: $qris->id,
                referenceCode: 'QRIS - '.$qris->merchant_name.' (NMID: '.$qris->nmid.')',
                reason: request('reason') ?? 'Penghapusan arsip gambar QRIS oleh admin'
            );

            $isReferencedElsewhere = QrisSetting::where('id', '!=', $qris->id)->where('image_path', $imagePath)->exists();
            if (! $isReferencedElsewhere && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $qris->delete();

        return redirect()->route('admin.qris.index')
            ->with('success', 'Konfigurasi dan gambar QRIS non-aktif berhasil dihapus.');
    }
}
