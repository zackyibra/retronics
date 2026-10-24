<?php

namespace Tests\Feature;

use App\Models\Fine;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalFineTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->customer = User::where('role', 'customer')->first();
        $this->product = Product::where('stock', '>', 0)->first();
    }

    /**
     * Helper to create a rental with in_use status.
     */
    protected function createInUseRental(string $endDate, int $quantity = 1): Rental
    {
        $startDate = Carbon::parse($endDate)->subDays(2)->toDateString();

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-TEST-'.uniqid(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => 3,
            'total_price' => 300000,
            'status' => 'in_use',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
            'price_per_day' => 100000,
            'subtotal' => 300000,
        ]);

        return $rental;
    }

    /**
     * 1. Pengembalian tepat waktu menghasilkan denda keterlambatan nol.
     */
    public function test_ontime_return_results_in_zero_late_fine(): void
    {
        $today = Carbon::today()->toDateString();
        $rental = $this->createInUseRental($today);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => $today,
            'admin_notes' => 'Pengembalian tepat waktu kondisi prima.',
        ]);

        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals('completed', $rental->status);
        $this->assertNotNull($rental->returned_at);
        $this->assertDatabaseCount('fines', 0);
        $this->assertEquals(0.0, $rental->total_fines);
        $this->assertNull($rental->late_fine);
    }

    /**
     * 2. Pengembalian terlambat menghasilkan denda sesuai rumus.
     */
    public function test_late_return_calculates_fine_according_to_formula(): void
    {
        // Rental ended 3 days ago, returned today (3 days late)
        $endDate = Carbon::today()->subDays(3)->toDateString();
        $returnedAt = Carbon::today()->toDateString();

        $rental = $this->createInUseRental($endDate);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => $returnedAt,
            'admin_notes' => 'Terlambat 3 hari.',
        ]);

        $response->assertSessionHas('success');

        $ratePerDay = (float) config('rental.late_fee_per_day', 50000);
        $expectedAmount = 3 * $ratePerDay;

        $this->assertDatabaseHas('fines', [
            'rental_id' => $rental->id,
            'rental_item_id' => null,
            'type' => Fine::TYPE_LATE,
            'days_late' => 3,
            'rate_per_day' => $ratePerDay,
            'amount' => $expectedAmount,
            'assessed_by' => $this->admin->id,
        ]);

        $rental->refresh();
        $this->assertEquals($expectedAmount, $rental->total_fines);
        $this->assertNotNull($rental->late_fine);
        $this->assertEquals(3, $rental->late_fine->days_late);
    }

    /**
     * 3. Denda kerusakan dapat ditetapkan oleh admin.
     */
    public function test_damage_fine_can_be_assessed_by_admin(): void
    {
        $today = Carbon::today()->toDateString();
        $rental = $this->createInUseRental($today);
        $item = $rental->items->first();

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'perawatan_rusak',
            'returned_at' => $today,
            'damage_fines' => [
                $item->id => 125000,
            ],
            'damage_reasons' => [
                $item->id => 'Goresan cukup dalam pada bodi samping kamera',
            ],
            'admin_notes' => 'Perlu servis casing.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fines', [
            'rental_id' => $rental->id,
            'rental_item_id' => $item->id,
            'type' => Fine::TYPE_DAMAGE,
            'amount' => 125000,
            'reason' => 'Goresan cukup dalam pada bodi samping kamera',
            'assessed_by' => $this->admin->id,
        ]);

        $rental->refresh();
        $this->assertEquals(1, $rental->damage_fines->count());
        $this->assertEquals(125000, $rental->total_fines);
    }

    /**
     * 4. Denda kerusakan tidak boleh bernilai negatif.
     */
    public function test_damage_fine_cannot_be_negative(): void
    {
        $today = Carbon::today()->toDateString();
        $rental = $this->createInUseRental($today);
        $item = $rental->items->first();

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => $today,
            'damage_fines' => [
                $item->id => -50000,
            ],
        ]);

        $response->assertSessionHasErrors("damage_fines.{$item->id}");
        $this->assertDatabaseCount('fines', 0);

        $rental->refresh();
        $this->assertEquals('in_use', $rental->status);
    }

    /**
     * 5. Total denda merupakan jumlah seluruh komponen denda yang benar.
     */
    public function test_total_fine_is_sum_of_all_fine_components(): void
    {
        // 2 days late
        $endDate = Carbon::today()->subDays(2)->toDateString();
        $rental = $this->createInUseRental($endDate);

        // Add a second item to the rental
        $secondProduct = Product::where('id', '!=', $this->product->id)->where('stock', '>', 0)->first();
        $secondItem = RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $secondProduct->id,
            'quantity' => 1,
            'price_per_day' => 150000,
            'subtotal' => 450000,
        ]);

        $firstItem = $rental->items->first();

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'perawatan_rusak',
            'returned_at' => Carbon::today()->toDateString(),
            'damage_fines' => [
                $firstItem->id => 60000,
                $secondItem->id => 90000,
            ],
            'damage_reasons' => [
                $firstItem->id => 'Kabel data putus',
                $secondItem->id => 'Cap lensa hilang',
            ],
        ]);

        $response->assertSessionHas('success');

        $ratePerDay = (float) config('rental.late_fee_per_day', 50000);
        $expectedLate = 2 * $ratePerDay; // 100,000
        $expectedDamage = 60000 + 90000; // 150,000
        $expectedTotal = $expectedLate + $expectedDamage; // 250,000

        $rental->refresh();
        $this->assertEquals($expectedTotal, $rental->total_fines);
        $this->assertEquals($expectedLate, $rental->late_fine->amount);
        $this->assertEquals(2, $rental->damage_fines->count());

        // Ensure rental main price is not overwritten
        $this->assertEquals(300000, (float) $rental->total_price);
    }

    /**
     * 6. Memproses ulang pengembalian tidak membuat denda atau pengurangan/penambahan stok ganda.
     */
    public function test_reprocessing_return_does_not_duplicate_fines_or_stock_changes(): void
    {
        $endDate = Carbon::today()->subDays(1)->toDateString();
        $rental = $this->createInUseRental($endDate, quantity: 1);
        $initialStock = $this->product->stock;

        $this->actingAs($this->admin);

        // First process
        $response1 = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => Carbon::today()->toDateString(),
        ]);
        $response1->assertSessionHas('success');

        $this->product->refresh();
        $this->assertEquals($initialStock + 1, $this->product->stock);
        $this->assertDatabaseCount('fines', 1);

        // Attempt second process
        $response2 = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => Carbon::today()->toDateString(),
        ]);
        $response2->assertSessionHas('error');

        // Stock and fine count must NOT change
        $this->product->refresh();
        $this->assertEquals($initialStock + 1, $this->product->stock);
        $this->assertDatabaseCount('fines', 1);
    }

    /**
     * 7. Pengguna biasa tidak dapat mengubah atau menetapkan denda.
     */
    public function test_regular_user_cannot_assess_or_modify_fines(): void
    {
        $rental = $this->createInUseRental(Carbon::today()->toDateString());

        $this->actingAs($this->customer);

        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'damage_fines' => [
                $rental->items->first()->id => 500000,
            ],
        ]);

        // Non-admin blocked by AdminMiddleware
        $response->assertRedirect(route('account.index'));
        $this->assertDatabaseCount('fines', 0);
    }

    /**
     * 8. Pengguna tidak dapat melihat transaksi milik pengguna lain.
     */
    public function test_user_cannot_view_other_users_rental_transaction(): void
    {
        $otherCustomer = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $rental = $this->createInUseRental(Carbon::today()->toDateString());
        // Rental belongs to $this->customer

        // Log in as other customer
        $this->actingAs($otherCustomer);

        $response = $this->get(route('rentals.show', $rental->id));
        $response->assertStatus(403);
    }

    /**
     * 9. Stok dan kondisi produk tetap konsisten.
     */
    public function test_product_stock_and_condition_remain_consistent(): void
    {
        $today = Carbon::today()->toDateString();
        $rental = $this->createInUseRental($today, quantity: 2);
        $initialStock = $this->product->stock;

        $this->actingAs($this->admin);

        // Test damaged return: stock should NOT increase, condition becomes maintenance
        $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'perawatan_rusak',
            'returned_at' => $today,
        ]);

        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);
        $this->assertEquals('Dalam Perawatan / Perbaikan', $this->product->condition);
    }

    /**
     * 10. Fitur lama tetap berjalan (halaman rincian menampilkan denda tanpa merusak layout).
     */
    public function test_rental_show_view_displays_fines_cleanly(): void
    {
        $endDate = Carbon::today()->subDays(2)->toDateString();
        $rental = $this->createInUseRental($endDate);
        $item = $rental->items->first();

        $this->actingAs($this->admin);

        $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'returned_at' => Carbon::today()->toDateString(),
            'damage_fines' => [
                $item->id => 75000,
            ],
            'damage_reasons' => [
                $item->id => 'Tombol shutter macet',
            ],
        ]);

        // View as owner customer
        $this->actingAs($this->customer);

        $response = $this->get(route('rentals.show', $rental->id));
        $response->assertStatus(200)
            ->assertSee('Rincian Denda Transaksi')
            ->assertSee('Denda Keterlambatan')
            ->assertSee('Denda Kerusakan')
            ->assertSee('Tombol shutter macet')
            ->assertSee('Rp'.number_format($rental->refresh()->total_fines, 0, ',', '.'));
    }
}
