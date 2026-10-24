<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    protected $model = Fine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'rental_item_id' => null,
            'type' => Fine::TYPE_LATE,
            'amount' => 50000,
            'days_late' => 1,
            'rate_per_day' => 50000,
            'reason' => 'Keterlambatan pengembalian 1 hari',
            'assessed_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the fine is for damage.
     */
    public function damage(?RentalItem $item = null): static
    {
        return $this->state(fn (array $attributes) => [
            'rental_item_id' => $item?->id,
            'type' => Fine::TYPE_DAMAGE,
            'amount' => 150000,
            'days_late' => null,
            'rate_per_day' => null,
            'reason' => 'Kerusakan fisik pada komponen barang',
        ]);
    }
}
