<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
            'amount' => fake()->randomElement([150000, 300000, 450000, 750000]),
            'payment_method' => fake()->randomElement(['transfer_bca', 'transfer_mandiri', 'qris']),
            'payment_date' => now(),
            'payment_proof' => 'payments/dummy-proof.jpg',
            'status' => Payment::STATUS_WAITING_VERIFICATION,
            'verified_by' => null,
            'verified_at' => null,
            'notes' => null,
        ];
    }

    /**
     * State for success status.
     */
    public function success(?User $admin = null): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_SUCCESS,
            'verified_by' => $admin?->id,
            'verified_at' => now(),
            'notes' => 'Pembayaran diverifikasi valid.',
        ]);
    }

    /**
     * State for rejected status.
     */
    public function rejected(?User $admin = null, string $reason = 'Bukti pembayaran tidak terbaca.'): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_REJECTED,
            'verified_by' => $admin?->id,
            'verified_at' => now(),
            'notes' => $reason,
        ]);
    }
}
