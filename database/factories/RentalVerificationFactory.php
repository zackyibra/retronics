<?php

namespace Database\Factories;

use App\Models\Rental;
use App\Models\RentalVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RentalVerification>
 */
class RentalVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'user_id' => User::factory(),
            'birth_date' => '2000-01-01',
            'id_card_number' => fake()->numerify('################'),
            'id_card_file' => 'private/verifications/ktp/dummy.jpg',
            'rental_purpose' => fake()->sentence(4),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation' => 'Keluarga',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agreement_signed_at' => now(),
            'agreement_ip_address' => fake()->ipv4(),
            'requires_stamp_duty' => false,
            'stamp_duty_amount' => 0,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ];
    }
}
