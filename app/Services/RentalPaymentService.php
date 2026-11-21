<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RentalPaymentService
{
    /**
     * Setujui pembayaran sewa, potong stok barang, dan ubah status pesanan ke confirmed.
     *
     * @return array{success: bool, message: string}
     */
    public function approvePayment(Rental $rental, ?Payment $payment = null, ?string $notes = null, bool $approveDocument = false): array
    {
        // Proteksi Dokumen: Wajib disetujui sebelum pembayaran disetujui
        if (! $rental->verification) {
            return [
                'success' => false,
                'message' => 'Pembayaran tidak dapat disetujui: Pesanan sewa belum memiliki data dokumen verifikasi identitas.',
            ];
        }

        if ($rental->verification->verification_status !== RentalVerification::STATUS_DISETUJUI) {
            if ($rental->verification->verification_status === RentalVerification::STATUS_MENUNGGU && $approveDocument) {
                $rental->verification->update([
                    'verification_status' => RentalVerification::STATUS_DISETUJUI,
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'admin_notes' => 'Disetujui bersamaan dengan verifikasi pembayaran.',
                ]);

                $rental->verification->load(['user', 'rental']);
                WhatsAppService::sendVerificationStatusNotification($rental->verification);
            } else {
                $statusText = match ($rental->verification->verification_status) {
                    RentalVerification::STATUS_MENUNGGU => 'masih menunggu verifikasi admin',
                    RentalVerification::STATUS_PERLU_REVISI => 'membutuhkan revisi dari penyewa',
                    RentalVerification::STATUS_DITOLAK => 'telah ditolak',
                    default => 'belum berstatus disetujui sah',
                };

                return [
                    'success' => false,
                    'message' => "Pembayaran tidak dapat disetujui: Dokumen identitas pelanggan {$statusText}. Harap periksa dan setujui dokumen verifikasi terlebih dahulu.",
                ];
            }
        }

        $adminNotes = $notes ?? 'Pembayaran diverifikasi valid oleh admin. Pesanan dikonfirmasi.';

        // Cek ketersediaan stok semua barang dengan lockForUpdate
        foreach ($rental->items as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            if (! $product || $product->stock < $item->quantity) {
                $prodName = $product ? $product->name : 'Barang';
                $available = $product ? $product->stock : 0;

                return [
                    'success' => false,
                    'message' => "Pembayaran tidak dapat disetujui: Stok untuk '{$prodName}' tidak mencukupi (sisa {$available}, dibutuhkan {$item->quantity}).",
                ];
            }
        }

        // Potong stok
        foreach ($rental->items as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            $product?->decrement('stock', $item->quantity);
        }

        // Update atau buat record pembayaran
        $targetPayment = $payment
            ?? $rental->payments()->where('status', Payment::STATUS_WAITING_VERIFICATION)->latest()->lockForUpdate()->first()
            ?? $rental->latestPayment;

        if ($targetPayment) {
            $targetPayment->update([
                'status' => Payment::STATUS_SUCCESS,
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
                'notes' => $adminNotes,
            ]);
        } else {
            Payment::create([
                'rental_id' => $rental->id,
                'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                'amount' => $rental->total_price,
                'payment_method' => $rental->payment_method ?? 'transfer_bca',
                'payment_date' => Carbon::now(),
                'payment_proof' => $rental->payment_proof,
                'status' => Payment::STATUS_SUCCESS,
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
                'notes' => $adminNotes,
            ]);
        }

        $rental->update([
            'status' => 'confirmed',
            'admin_notes' => $adminNotes,
        ]);

        return [
            'success' => true,
            'message' => 'Pembayaran valid! Status penyewaan telah dikonfirmasi dan stok telah direservasi.',
        ];
    }

    /**
     * Tolak bukti pembayaran dan kembalikan status sewa ke pending_payment agar penyewa dapat unggah ulang.
     *
     * @return array{success: bool, message: string}
     */
    public function rejectPayment(Rental $rental, ?Payment $payment = null, ?string $reason = null): array
    {
        $rejectionReason = $reason ?? 'Bukti pembayaran tidak sesuai atau kurang lengkap. Mohon upload ulang.';

        $targetPayment = $payment
            ?? $rental->payments()->where('status', Payment::STATUS_WAITING_VERIFICATION)->latest()->lockForUpdate()->first()
            ?? $rental->latestPayment;

        if ($targetPayment) {
            $targetPayment->update([
                'status' => Payment::STATUS_REJECTED,
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
                'notes' => $rejectionReason,
            ]);
        } else {
            Payment::create([
                'rental_id' => $rental->id,
                'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                'amount' => $rental->total_price,
                'payment_method' => $rental->payment_method ?? 'transfer_bca',
                'payment_date' => Carbon::now(),
                'payment_proof' => $rental->payment_proof,
                'status' => Payment::STATUS_REJECTED,
                'verified_by' => Auth::id(),
                'verified_at' => Carbon::now(),
                'notes' => $rejectionReason,
            ]);
        }

        $rental->update([
            'status' => 'pending_payment',
            'admin_notes' => $rejectionReason,
        ]);

        return [
            'success' => true,
            'message' => 'Status dikembalikan ke menunggu pembayaran dengan catatan untuk penyewa.',
        ];
    }
}
