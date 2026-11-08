<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    /**
     * 1. Tampilkan formulir permintaan OTP lupa kata sandi.
     */
    public function showRequestForm(): View|RedirectResponse
    {
        return view('auth.otp-request');
    }

    /**
     * 2. Kirim kode OTP 6 digit ke nomor WhatsApp pengguna.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_identifier' => ['required', 'string', 'max:100'],
        ], [
            'account_identifier.required' => 'Masukkan nomor WhatsApp atau email akun Anda.',
        ]);

        $identifier = trim($validated['account_identifier']);

        // Cari user berdasarkan nomor WhatsApp atau alamat email
        $user = User::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (! $user) {
            return back()->withInput()->withErrors([
                'account_identifier' => 'Akun dengan nomor WhatsApp atau email tersebut tidak ditemukan di sistem Retronics.',
            ]);
        }

        if (empty($user->phone)) {
            return back()->withInput()->withErrors([
                'account_identifier' => 'Akun ini belum memiliki nomor WhatsApp terdaftar. Hubungi admin store untuk bantuan pemulihan.',
            ]);
        }

        // Rate Limiting: Cegah spam permintaan OTP dalam kurun waktu 60 detik
        $recentOtp = PasswordResetOtp::where('phone', $user->phone)
            ->where('created_at', '>=', Carbon::now()->subSeconds(60))
            ->first();

        if ($recentOtp) {
            $secondsLeft = 60 - Carbon::now()->diffInSeconds($recentOtp->created_at);

            return back()->withInput()->withErrors([
                'account_identifier' => "Mohon tunggu {$secondsLeft} detik lagi sebelum meminta kode OTP berikutnya.",
            ]);
        }

        // Generate 6 digit OTP acak
        $otpCode = (string) random_int(100000, 999999);

        // Simpan ke database MySQL dengan masa berlaku 5 menit (menggunakan Hash::make demi keamanan)
        PasswordResetOtp::create([
            'phone' => $user->phone,
            'otp' => Hash::make($otpCode),
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_verified' => false,
        ]);

        // Kirimkan via WhatsApp Gateway Service
        $sendResult = WhatsAppService::sendOtp($user->phone, $otpCode, $user->name);

        session(['otp_phone' => $user->phone]);

        // Simpan kode di session jika dalam mode lokal/demo agar mudah diuji oleh penguji
        if (! app()->environment('production')) {
            session(['demo_otp_hint' => $otpCode]);
        }

        $maskedPhone = substr($user->phone, 0, 4).'****'.substr($user->phone, -3);

        return redirect()->route('otp.verify')
            ->with('success', "Kode verifikasi OTP 6 digit berhasil dikirimkan ke WhatsApp Anda ({$maskedPhone}).");
    }

    /**
     * 3. Tampilkan formulir input verifikasi 6 digit OTP.
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        $phone = session('otp_phone');

        if (! $phone) {
            return redirect()->route('otp.request')
                ->with('error', 'Sesi verifikasi telah berakhir. Silakan masukkan nomor WhatsApp Anda kembali.');
        }

        $maskedPhone = substr($phone, 0, 4).'****'.substr($phone, -3);

        return view('auth.otp-verify', compact('phone', 'maskedPhone'));
    }

    /**
     * 4. Validasi kode OTP yang dimasukkan pengguna.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ], [
            'otp.required' => 'Kode OTP 6 digit wajib diisi.',
            'otp.digits' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $phone = session('otp_phone');

        if (! $phone) {
            return redirect()->route('otp.request')
                ->with('error', 'Sesi telah kedaluwarsa. Silakan masukkan nomor kembali.');
        }

        $otpRecord = PasswordResetOtp::where('phone', $phone)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors([
                'otp' => 'Kode OTP telah kedaluwarsa atau tidak valid. Silakan minta kode baru.',
            ]);
        }

        if ($otpRecord->hasExceededAttempts(5)) {
            return back()->withErrors([
                'otp' => 'Anda telah melebihi batas 5 kali percobaan salah. Silakan minta kode OTP baru.',
            ]);
        }

        if (! $otpRecord->isValid($request->input('otp'))) {
            $otpRecord->increment('attempts');
            $remaining = 5 - $otpRecord->attempts;

            return back()->withErrors([
                'otp' => "Kode OTP yang Anda masukkan salah. Sisa kesempatan: {$remaining}x percobaan.",
            ]);
        }

        // Tandai OTP telah terverifikasi
        $otpRecord->update(['is_verified' => true]);
        session(['otp_verified_phone' => $phone]);

        return redirect()->route('otp.reset')
            ->with('success', 'Kode OTP berhasil diverifikasi! Silakan tetapkan kata sandi baru Anda.');
    }

    /**
     * 5. Tampilkan formulir pembuatan kata sandi baru.
     */
    public function showResetPasswordForm(): View|RedirectResponse
    {
        $verifiedPhone = session('otp_verified_phone');

        if (! $verifiedPhone) {
            return redirect()->route('otp.request')
                ->with('error', 'Akses tidak sah. Silakan verifikasi nomor WhatsApp Anda terlebih dahulu.');
        }

        return view('auth.otp-reset');
    }

    /**
     * 6. Simpan kata sandi baru ke database MySQL.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $verifiedPhone = session('otp_verified_phone');

        if (! $verifiedPhone) {
            return redirect()->route('otp.request')
                ->with('error', 'Sesi verifikasi telah berakhir.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::where('phone', $verifiedPhone)->firstOrFail();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Hapus semua riwayat OTP untuk nomor ini demi keamanan
        PasswordResetOtp::where('phone', $verifiedPhone)->delete();

        // Bersihkan sesi
        session()->forget(['otp_phone', 'otp_verified_phone', 'demo_otp_hint']);

        return redirect()->route('login')
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}
