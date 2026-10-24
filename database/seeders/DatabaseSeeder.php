<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@retronics.test'],
            [
                'name' => 'Admin Retronics',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Store Retronics Studio',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@retronics.test'],
            [
                'name' => 'Budi Santoso',
                'password' => bcrypt('password123'),
                'role' => 'customer',
                'phone' => '085712345678',
                'address' => 'Jl. Mawar No. 12, Jakarta',
                'id_card_number' => '3271012345670001',
            ]
        );

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
