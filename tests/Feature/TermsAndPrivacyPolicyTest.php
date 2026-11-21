<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermsAndPrivacyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_terms_and_conditions_page(): void
    {
        $response = $this->get(route('terms'));

        $response->assertOk();
        $response->assertSee('Syarat dan Ketentuan Layanan');
        $response->assertSee('Persyaratan Usia Minimal');
        $response->assertSee('17 tahun');
        $response->assertSee('Rp50.000');
    }

    public function test_user_can_access_privacy_policy_page(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('Nomor 27 Tahun 2022');
        $response->assertSee('Pelindungan Data Pribadi');
    }

    public function test_registration_requires_agreeing_to_terms(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budisantoso_test@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // agree_terms intentionally omitted
        ]);

        $response->assertSessionHasErrors('agree_terms');
        $this->assertDatabaseMissing('users', ['email' => 'budisantoso_test@example.com']);
    }

    public function test_registration_saves_terms_agreement_timestamp_and_version(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budisantoso_test@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_terms' => '1',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'budisantoso_test@example.com',
            'terms_version' => 'v1.0-2026',
        ]);

        $user = User::where('email', 'budisantoso_test@example.com')->first();
        $this->assertNotNull($user->terms_agreed_at);
    }
}
