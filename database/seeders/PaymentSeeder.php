<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $rentals = Rental::all();

        foreach ($rentals as $rental) {
            // Only seed if no payment exists for this rental
            if ($rental->payments()->exists()) {
                continue;
            }

            if (in_array($rental->status, ['confirmed', 'preparing', 'ready_pickup', 'in_use', 'returned', 'completed'])) {
                Payment::create([
                    'rental_id' => $rental->id,
                    'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                    'amount' => $rental->total_price,
                    'payment_method' => $rental->payment_method ?? 'transfer_bca',
                    'payment_date' => $rental->created_at->copy()->addMinutes(15),
                    'payment_proof' => $rental->payment_proof,
                    'status' => Payment::STATUS_SUCCESS,
                    'verified_by' => $admin?->id,
                    'verified_at' => $rental->created_at->copy()->addMinutes(30),
                    'notes' => 'Pembayaran terverifikasi valid otomatis.',
                ]);
            } elseif ($rental->status === 'waiting_verification') {
                Payment::create([
                    'rental_id' => $rental->id,
                    'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                    'amount' => $rental->total_price,
                    'payment_method' => $rental->payment_method ?? 'transfer_bca',
                    'payment_date' => Carbon::now()->subHours(2),
                    'payment_proof' => $rental->payment_proof,
                    'status' => Payment::STATUS_WAITING_VERIFICATION,
                    'verified_by' => null,
                    'verified_at' => null,
                    'notes' => null,
                ]);
            }
        }
    }
}
