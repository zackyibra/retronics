<?php

namespace App\Services;

use App\Models\RentalVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor ponsel Indonesia menjadi format internasional (628xxx).
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Hilangkan semua karakter non-digit
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali dengan '08', ganti '0' dengan '62'
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        }

        // Jika diawali dengan '8' langsung, tambahkan '62' di depan
        if (str_starts_with($cleaned, '8')) {
            $cleaned = '62'.$cleaned;
        }

        return $cleaned;
    }

    /**
     * Kirimkan pesan umum ke nomor WhatsApp.
     *
     * @return array{success: bool, message: string, simulated: bool}
     */
    public static function sendMessage(string $targetPhone, string $message): array
    {
        $formattedPhone = self::formatPhoneNumber($targetPhone);
        $token = env('FONNTE_TOKEN', config('services.fonnte.token'));

        if (empty($token) || app()->environment('testing')) {
            $sanitizedMessage = preg_replace('/\b\d{6}\b/', '******', $message);
            Log::info("[WhatsApp Simulator] Mengirim ke {$formattedPhone}: {$sanitizedMessage}");

            return [
                'success' => true,
                'message' => "Pesan berhasil disimulasikan untuk {$formattedPhone}.",
                'simulated' => true,
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post('https://api.fonnte.com/send', [
                    'target' => $formattedPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim.',
                    'simulated' => false,
                ];
            }

            Log::error('Fonnte WhatsApp API Error: '.$response->body());

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp ke gateway provider.',
                'simulated' => false,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Service Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kendala jaringan saat menghubungi layanan WhatsApp.',
                'simulated' => false,
            ];
        }
    }

    /**
     * Kirimkan pesan kode OTP verifikasi ke nomor WhatsApp pengguna.
     *
     * @return array{success: bool, message: string, simulated: bool}
     */
    public static function sendOtp(string $targetPhone, string $otpCode, string $userName = 'Pelanggan'): array
    {
        $message = "Halo *{$userName}*,\n\n"
                 ."Berikut adalah kode verifikasi OTP pemulihan akun *Retronics – Rental Fotografi* Anda:\n\n"
                 ."*{$otpCode}*\n\n"
                 ."Kode ini berlaku selama *5 menit*. Jangan bagikan kode ini kepada siapa pun termasuk pihak Retronics demi keamanan akun Anda.\n\n"
                 .'Jika Anda tidak merasa meminta kode ini, abaikan pesan ini.';

        return self::sendMessage($targetPhone, $message);
    }

    /**
     * Kirimkan notifikasi pembaruan status verifikasi dokumen ke WhatsApp pelanggan.
     *
     * @return array{success: bool, message: string, simulated: bool}
     */
    public static function sendVerificationStatusNotification(RentalVerification $verification): array
    {
        $user = $verification->user;
        if (! $user || ! $user->phone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp pengguna tidak tersedia.',
                'simulated' => false,
            ];
        }

        $rental = $verification->rental;
        $rentalCode = $rental ? $rental->rental_code : 'Sewa';
        $statusLabel = $verification->status_label;
        $notes = $verification->admin_notes ? "\n*Catatan Admin:* {$verification->admin_notes}" : '';

        $message = "Halo *{$user->name}*,\n\n"
                 ."Status verifikasi dokumen KTP Anda untuk pesanan *{$rentalCode}* di Retronics telah diperbarui:\n\n"
                 ."📌 Status: *{$statusLabel}*{$notes}\n\n";

        if ($verification->isApproved()) {
            $message .= 'Dokumen KTP Anda telah disetujui. Silakan lanjutkan ke pembayaran agar pesanan dapat disiapkan tim kami.';
        } elseif ($verification->needsRevision()) {
            $message .= 'Harap periksa catatan admin dan unggah kembali foto KTP yang sesuai melalui menu Rincian Sewa pada akun Anda.';
        } elseif ($verification->isRejected()) {
            $message .= 'Mohon maaf, pengajuan sewa Anda tidak dapat diproses karena dokumen tidak memenuhi syarat verifikasi.';
        }

        return self::sendMessage($user->phone, $message);
    }
}
