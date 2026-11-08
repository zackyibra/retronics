<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed();
    }

    /**
     * Test halaman publik promo menampilkan kode promo aktif dari database.
     */
    public function test_promotions_page_displays_active_promotions_from_database(): void
    {
        $response = $this->get(route('promo'));

        $response->assertStatus(200)
            ->assertSee('RETRONICS10')
            ->assertSee('SEWAHEMAT')
            ->assertSee('WEEKENDPROMO');
    }

    /**
     * Test proses sewa dengan kode promo valid memotong harga dan mencatat promo ke rentals & promotions table.
     */
    public function test_checkout_with_valid_promo_stores_discount_and_increments_quota(): void
    {
        $user = User::where('role', 'customer')->first();
        $this->actingAs($user);

        $product = Product::where('price_per_day', '>=', 100000)->first();
        $initialPromo = Promotion::where('code', 'RETRONICS10')->first();
        $initialQuotaUsed = $initialPromo->quota_used;

        // Sewa 3 hari @ min Rp100.000 = Rp300.000 gross (memenuhi syarat min Rp200.000)
        $response = $this->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 3,
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                ],
            ],
            'promo_code' => 'RETRONICS10',
            'notes' => 'Catatan test promo',
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345670001',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Produksi video promo',
            'emergency_contact_name' => 'Agus',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Kerabat',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agree_terms' => 1,
        ]);

        $response->assertRedirect();

        $rental = Rental::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($rental);
        $this->assertEquals('RETRONICS10', $rental->promo_code);
        $this->assertGreaterThan(0, $rental->discount_amount);

        // Pastikan kuota pemakaian promo dinaikkan di MySQL
        $initialPromo->refresh();
        $this->assertEquals($initialQuotaUsed + 1, $initialPromo->quota_used);
    }

    /**
     * Test admin dapat mengelola (CRUD) promo di panel backoffice.
     */
    public function test_admin_can_manage_promotions_crud(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // 1. Buka Halaman Kelola Promo
        $response = $this->get(route('admin.promotions.index'));
        $response->assertStatus(200)->assertSee('Kelola promo');

        // 2. Tambah Promo Baru
        $response = $this->post(route('admin.promotions.store'), [
            'code' => 'FLASH50',
            'name' => 'Flash Sale Kreator',
            'description' => 'Diskon Rp50.000 untuk produksi video.',
            'type' => 'fixed',
            'discount_value' => 50000,
            'min_transaction' => 250000,
            'quota_total' => 25,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addMonths(2)->toDateString(),
            'is_active' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('promotions', [
            'code' => 'FLASH50',
            'discount_value' => 50000,
        ]);

        $newPromo = Promotion::where('code', 'FLASH50')->first();

        // 3. Update Promo
        $response = $this->put(route('admin.promotions.update', $newPromo->id), [
            'code' => 'FLASH50',
            'name' => 'Flash Sale Update',
            'description' => 'Deskripsi update',
            'type' => 'fixed',
            'discount_value' => 60000,
            'min_transaction' => 300000,
            'quota_total' => 30,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addMonths(2)->toDateString(),
            'is_active' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('promotions', [
            'id' => $newPromo->id,
            'discount_value' => 60000,
        ]);

        // 4. Hapus Promo
        $response = $this->delete(route('admin.promotions.destroy', $newPromo->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('promotions', ['id' => $newPromo->id]);
    }
}
