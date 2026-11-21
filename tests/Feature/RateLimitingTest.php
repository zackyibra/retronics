<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_route_is_throttled_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'wrong@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be throttled (HTTP 429)
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_admin_login_route_is_throttled_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email' => 'wrongadmin@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'wrongadmin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_register_route_is_throttled_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [
                'name' => 'Spam Bot',
                'email' => "bot{$i}@example.com",
                'phone' => "08123456789{$i}",
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'agree_terms' => true,
            ]);
            auth()->logout();
        }

        $response = $this->post('/register', [
            'name' => 'Spam Bot',
            'email' => 'bot6@example.com',
            'phone' => '081234567896',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'agree_terms' => true,
        ]);

        $response->assertStatus(429);
    }
}
