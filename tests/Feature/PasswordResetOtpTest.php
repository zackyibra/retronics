<?php

namespace Tests\Feature;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test formulir permintaan OTP dapat dibuka oleh pengguna.
     */
    public function test_user_can_view_otp_request_page(): void
    {
        $response = $this->get(route('otp.request'));

        $response->assertStatus(200)
            ->assertSee('Lupa Kata Sandi?')
            ->assertSee('Nomor WhatsApp atau Email Terdaftar');
    }

    /**
     * Test pengguna dapat meminta kode OTP menggunakan nomor WhatsApp.
     */
    public function test_user_can_request_otp_using_phone_number(): void
    {
        $user = User::whereNotNull('phone')->firstOrFail();

        $response = $this->post(route('otp.send'), [
            'account_identifier' => $user->phone,
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertEquals($user->phone, session('otp_phone'));

        $this->assertDatabaseHas('password_reset_otps', [
            'phone' => $user->phone,
            'is_verified' => false,
        ]);
    }

    /**
     * Test pengguna dapat meminta kode OTP menggunakan alamat email terdaftar.
     */
    public function test_user_can_request_otp_using_email_address(): void
    {
        $user = User::whereNotNull('phone')->whereNotNull('email')->firstOrFail();

        $response = $this->post(route('otp.send'), [
            'account_identifier' => $user->email,
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertEquals($user->phone, session('otp_phone'));

        $this->assertDatabaseHas('password_reset_otps', [
            'phone' => $user->phone,
        ]);
    }

    /**
     * Test permintaan OTP gagal jika identitas akun tidak terdaftar.
     */
    public function test_unregistered_identifier_returns_validation_error(): void
    {
        $response = $this->post(route('otp.send'), [
            'account_identifier' => '0899999999999',
        ]);

        $response->assertSessionHasErrors(['account_identifier']);
    }

    /**
     * Test pengguna dapat memverifikasi kode OTP yang valid.
     */
    public function test_user_can_verify_valid_otp(): void
    {
        $user = User::whereNotNull('phone')->firstOrFail();

        PasswordResetOtp::create([
            'phone' => $user->phone,
            'otp' => '654321',
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_verified' => false,
        ]);

        $response = $this->withSession(['otp_phone' => $user->phone])
            ->post(route('otp.verify.submit'), [
                'otp' => '654321',
            ]);

        $response->assertRedirect(route('otp.reset'));
        $this->assertEquals($user->phone, session('otp_verified_phone'));

        $this->assertDatabaseHas('password_reset_otps', [
            'phone' => $user->phone,
            'is_verified' => true,
        ]);
    }

    /**
     * Test verifikasi gagal jika kode OTP salah dan percobaan bertambah.
     */
    public function test_user_cannot_verify_invalid_otp(): void
    {
        $user = User::whereNotNull('phone')->firstOrFail();

        $otpRecord = PasswordResetOtp::create([
            'phone' => $user->phone,
            'otp' => '654321',
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_verified' => false,
        ]);

        $response = $this->withSession(['otp_phone' => $user->phone])
            ->post(route('otp.verify.submit'), [
                'otp' => '000000',
            ]);

        $response->assertSessionHasErrors(['otp']);

        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $otpRecord->id,
            'attempts' => 1,
            'is_verified' => false,
        ]);
    }

    /**
     * Test setelah verifikasi OTP, pengguna dapat mereset kata sandi dan login dengan kata sandi baru.
     */
    public function test_user_can_reset_password_after_verification_and_login_with_new_password(): void
    {
        $user = User::whereNotNull('phone')->firstOrFail();

        PasswordResetOtp::create([
            'phone' => $user->phone,
            'otp' => '654321',
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_verified' => true,
        ]);

        $newPassword = 'RetronicsSecure2026!';

        $response = $this->withSession([
            'otp_phone' => $user->phone,
            'otp_verified_phone' => $user->phone,
        ])->post(route('otp.reset.submit'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));

        // Verifikasi bahwa user bisa login dengan password baru
        $loginResponse = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $newPassword,
        ]);

        $loginResponse->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
