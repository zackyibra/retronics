<?php

namespace Database\Factories;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    protected $model = Rental::class;

    public function definition(): array
    {
        $startDate = now()->addDay();
        $duration = fake()->numberBetween(1, 5);
        $endDate = $startDate->copy()->addDays($duration - 1);

        return [
            'user_id' => User::factory(),
            'rental_code' => 'RET-'.date('Ymd').'-'.strtoupper(Str::random(5)),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'duration_days' => $duration,
            'total_price' => fake()->randomElement([150000, 300000, 450000, 600000]),
            'status' => 'pending_payment',
            'payment_method' => null,
            'payment_proof' => null,
            'notes' => null,
            'admin_notes' => null,
        ];
    }
}
