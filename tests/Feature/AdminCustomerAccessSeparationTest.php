<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerAccessSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_separation@retronics.test',
            'phone' => '081234567891',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer_separation@retronics.test',
            'phone' => '081234567892',
        ]);

        $category = Category::create([
            'name' => 'Kamera Bioskop',
            'slug' => 'kamera-bioskop',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sony FX3 Cinema Line',
            'slug' => 'sony-fx3-cinema-line',
            'price_per_day' => 750000,
            'stock' => 5,
            'is_active' => true,
        ]);
    }

    /* --------------------------------------------------------------------------
     * 1. PENGUJIAN ADMIN
     * -------------------------------------------------------------------------- */

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Tren omset terverifikasi');
        $response->assertViewHas('revenueTrendDays');
        $response->assertViewHas('revenueTrendTotal');
        $response->assertViewHas('revenueTrendRange');
    }

    public function test_admin_can_browse_public_pages(): void
    {
        // Beranda
        $this->actingAs($this->admin)->get(route('home'))->assertOk();

        // Katalog
        $this->actingAs($this->admin)->get(route('products.index'))->assertOk();

        // Detail Produk
        $this->actingAs($this->admin)->get(route('products.show', $this->product->slug))->assertOk();

        // Promo
        $this->actingAs($this->admin)->get(route('promo'))->assertOk();
    }

    public function test_admin_cannot_add_items_to_cart(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'duration_days' => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertEmpty(session('cart', []));
    }

    public function test_admin_cannot_checkout_cart(): void
    {
        // Even if cart session had data
        session(['cart' => [
            $this->product->id => [
                'quantity' => 1,
                'start_date' => Carbon::tomorrow()->toDateString(),
                'duration_days' => 2,
            ],
        ]]);

        $response = $this->actingAs($this->admin)->post(route('cart.checkout'));
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_access_rental_booking_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('rentals.create', [
            'product_id' => $this->product->id,
        ]));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_store_rental_transaction(): void
    {
        $response = $this->actingAs($this->admin)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 2,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'id_card_number' => '3201012345670001',
            'birth_date' => '1995-05-15',
            'rental_purpose' => 'Shooting Video',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_phone' => '081234567899',
            'emergency_contact_relation' => 'Saudara',
            'signature_data' => 'data:image/png;base64,sample',
            'agree_terms' => '1',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('rentals', ['user_id' => $this->admin->id]);
    }

    public function test_admin_product_page_displays_admin_notice_and_disables_rent_cta(): void
    {
        $response = $this->actingAs($this->admin)->get(route('products.show', $this->product->slug));

        $response->assertOk();
        $response->assertSee('Akun Administrator');
        $response->assertSee('Kelola Inventaris Barang di Panel Admin');
        $response->assertDontSee('⚡ Sewa Sekarang (Direct Checkout)');
    }

    /* --------------------------------------------------------------------------
     * 2. PENGUJIAN PELANGGAN
     * -------------------------------------------------------------------------- */

    public function test_customer_cannot_access_admin_dashboard_or_admin_routes(): void
    {
        $this->actingAs($this->customer)->get(route('admin.dashboard'))
            ->assertRedirect(route('account.index'))
            ->assertSessionHas('error');

        $this->actingAs($this->customer)->get(route('admin.products.index'))
            ->assertRedirect(route('account.index'))
            ->assertSessionHas('error');

        $this->actingAs($this->customer)->get(route('admin.rentals.index'))
            ->assertRedirect(route('account.index'))
            ->assertSessionHas('error');

        $this->actingAs($this->customer)->get(route('admin.qris.index'))
            ->assertRedirect(route('account.index'))
            ->assertSessionHas('error');
    }

    public function test_customer_can_add_to_cart_and_access_booking_form(): void
    {
        // Tambah ke keranjang
        $response = $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'duration_days' => 1,
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertNotEmpty(session('cart'));

        // Buka formulir booking
        $response = $this->actingAs($this->customer)->get(route('rentals.create', [
            'product_id' => $this->product->id,
        ]));

        $response->assertOk();
        $response->assertSee($this->product->name);
    }
}
