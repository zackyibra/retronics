<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RetronicsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed();
    }

    /**
     * Helper: builds the verification payload required by the new rental verification system.
     *
     * @return array<string, mixed>
     */
    private function verificationPayload(): array
    {
        return [
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345670001',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Produksi konten media sosial',
            'emergency_contact_name' => 'Agus Wijaya',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Saudara',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agree_terms' => 1,
        ];
    }

    /**
     * Diagram 1: Flowchart Pengunjung - Akses Beranda, Katalog, dan Detail.
     */
    public function test_visitor_flow_home_catalog_detail(): void
    {
        // 1. Buka Website Retronics -> Halaman Beranda
        $response = $this->get(route('home'));
        $response->assertStatus(200)
            ->assertSee('retronics')
            ->assertSee('Kalkulator sewa');

        // 2. Jelajahi Menu -> Lihat Katalog Barang
        $response = $this->get(route('products.index'));
        $response->assertStatus(200)
            ->assertSee('Katalog Peralatan Fotografi');

        // 3. Lihat Detail Barang
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->get(route('products.show', $product->slug));
        $response->assertStatus(200)
            ->assertSee($product->name)
            ->assertSee('Sewa Sekarang');
    }

    /**
     * Diagram 1 & 4: Flowchart Autentikasi (Login & Register).
     */
    public function test_authentication_login_and_register_flow(): void
    {
        // Cek halaman login
        $response = $this->get(route('login'));
        $response->assertStatus(200)->assertSee('Selamat datang kembali');

        // Login dengan data yang salah
        $response = $this->post(route('login'), [
            'email' => 'wrong@email.com',
            'password' => 'wrongpass',
        ]);
        $response->assertSessionHasErrors('email');

        // Login dengan data yang benar (Customer)
        $response = $this->post(route('login'), [
            'email' => 'user@retronics.test',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        // Logout
        $response = $this->post(route('logout'));
        $response->assertRedirect(route('home'));
        $this->assertGuest();

        // Pendaftaran Akun Baru
        $response = $this->post(route('register'), [
            'name' => 'Kreator Baru',
            'email' => 'kreator@example.com',
            'phone' => '089988776655',
            'address' => 'Studio Kreatif Jakarta',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_terms' => '1',
        ]);
        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'kreator@example.com']);
    }

    /**
     * Diagram 2: Flowchart Penyewa - Booking, Pembayaran, dan Timeline.
     */
    public function test_renter_rental_and_payment_flow(): void
    {
        $user = User::where('role', 'customer')->first();
        $product = Product::where('stock', '>', 0)->first();

        // Login ke akun
        $this->actingAs($user);

        // Buka form booking
        $response = $this->get(route('rentals.create', ['product_id' => $product->id]));
        $response->assertStatus(200)->assertSee('Konfirmasi Pemesanan');

        // Lakukan pemesanan (dengan field verifikasi baru)
        $response = $this->post(route('rentals.store'), array_merge([
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 3,
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                ],
            ],
            'notes' => 'Tolong siapkan baterai full charge.',
        ], $this->verificationPayload()));

        $rental = Rental::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($rental);
        $this->assertEquals('pending_payment', $rental->status);

        $response->assertRedirect(route('rentals.show', $rental->id));

        // Pengguna melakukan pembayaran (upload / konfirmasi)
        $response = $this->post(route('rentals.pay', $rental->id), [
            'payment_method' => 'transfer_bca',
            'payment_proof' => UploadedFile::fake()->image('bukti_bca.jpg'),
        ]);

        $response->assertRedirect(route('rentals.show', $rental->id));
        $rental->refresh();
        $this->assertEquals('waiting_verification', $rental->status);
    }

    /**
     * Diagram 3: Flowchart Admin - Kategori, Barang, Verifikasi, Pengembalian.
     */
    public function test_admin_flow_all_pillars(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Dashboard Admin
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200)->assertSee('Ringkasan operasional');

        // Pilar 1: Tambah Kategori
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Drone & Aerial',
            'description' => 'Peralatan fotografi udara',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Drone & Aerial']);

        // Pilar 2: Tambah Barang
        $category = Category::first();
        $response = $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Sony FX3 Cinema Line',
            'price_per_day' => 450000,
            'stock' => 2,
            'condition' => 'sangat baik',
            'description' => 'Kamera cinema full-frame ringkas untuk film.',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', ['name' => 'Sony FX3 Cinema Line']);

        // Pilar 3: Verifikasi Pembayaran Penyewaan Manual
        $customer = User::where('role', 'customer')->first();
        $rental = Rental::create([
            'user_id' => $customer->id,
            'rental_code' => 'RET-TEST-001',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 300000,
            'status' => 'waiting_verification',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $customer->id,
            'id_card_number' => '3578012345670001',
            'rental_purpose' => 'Test Flow Admin',
            'emergency_contact_name' => 'Kontak',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Kerabat',
            'signature_data' => 'data:image/png;base64,dummy',
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $response = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);
        $response->assertSessionHas('success');
        $rental->refresh();
        $this->assertEquals('confirmed', $rental->status);

        // Siapkan & Serahkan Barang
        $this->post(route('admin.rentals.update_status', $rental->id), ['status' => 'preparing']);
        $this->post(route('admin.rentals.update_status', $rental->id), ['status' => 'ready_pickup']);
        $this->post(route('admin.rentals.update_status', $rental->id), ['status' => 'in_use']);

        $rental->refresh();
        $this->assertEquals('in_use', $rental->status);

        // Pilar 4: Pengembalian - Periksa Kondisi & Tutup Transaksi
        $response = $this->post(route('admin.returns.process', $rental->id), [
            'item_condition_after' => 'layak_tersedia',
            'admin_notes' => 'Unit lengkap, bersih dan sensor normal.',
        ]);
        $response->assertSessionHas('success');
        $rental->refresh();
        $this->assertEquals('completed', $rental->status);
    }

    /**
     * Diagram 4: Flowchart Menu Akun dan Wishlist.
     */
    public function test_account_and_wishlist_flow(): void
    {
        $user = User::where('role', 'customer')->first();
        $product = Product::first();

        $this->actingAs($user);

        // 1. Akses Menu Akun
        $response = $this->get(route('account.index'));
        $response->assertStatus(200)->assertSee('Profil & Password');

        // 2. Edit Profil
        $response = $this->post(route('account.profile.update'), [
            'phone' => '081299998888',
            'address' => 'Jl. Pabuaran No. 99, Gunung Putri, Bogor',
            'id_card_number' => '3578012345670002',
        ]);
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('081299998888', $user->phone);

        // 3. Wishlist - Tambah ke favorit
        $response = $this->post(route('wishlist.toggle', $product->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // Buka halaman Wishlist
        $response = $this->get(route('account.wishlist'));
        $response->assertStatus(200)->assertSee($product->name);

        // Hapus dari wishlist
        $this->post(route('wishlist.toggle', $product->id));
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // 4. Riwayat Penyewaan
        $response = $this->get(route('account.rentals'));
        $response->assertStatus(200)->assertSee('Riwayat sewa');
    }

    /**
     * Test Pemisahan Login Admin dan Proteksi Middleware Otorisasi.
     */
    public function test_admin_separate_login_and_security(): void
    {
        // 1. Guest diarahkan ke /admin/login saat akses /admin/dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));

        // 2. Halaman Login Admin dapat diakses
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200)->assertSee('Portal Khusus Administrator');

        // 3. Pelanggan (Customer) mencoba login di /admin/login -> ditolak
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'user@retronics.test',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // 4. Admin login di /admin/login -> sukses ke /admin/dashboard
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@retronics.test',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        // 5. Pelanggan biasa tidak boleh mengakses /admin/*
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('account.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test Fitur Keranjang Penyewaan (Rental Cart) dan Checkout.
     */
    public function test_rental_cart_flow_and_checkout(): void
    {
        $product = Product::where('stock', '>', 1)->first();

        // 1. Buka keranjang saat kosong
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200)->assertSee('Keranjang Sewa Masih Kosong');

        // 2. Tambah produk ke keranjang
        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 3,
        ]);
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // Cek halaman keranjang terisi
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200)
            ->assertSee($product->name)
            ->assertSee('Estimasi biaya');

        // 3. Update keranjang
        $response = $this->post(route('cart.update'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'start_date' => Carbon::tomorrow()->toDateString(),
                    'duration_days' => 2,
                ],
            ],
        ]);
        $response->assertSessionHas('success');

        // 4. Checkout sebagai customer
        $user = User::where('role', 'customer')->first();
        $this->actingAs($user);

        $response = $this->post(route('cart.checkout'), [
            'notes' => 'Sewa via keranjang belanja',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rentals', [
            'user_id' => $user->id,
            'status' => 'pending_payment',
        ]);
        $this->assertEmpty(session('cart'));
    }

    /**
     * Test Fitur Admin: Pelanggan, Laporan, dan Pengaturan Akun.
     */
    public function test_admin_management_customers_reports_settings(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // 1. Kelola Pelanggan
        $response = $this->get(route('admin.customers.index'));
        $response->assertStatus(200)
            ->assertSee('Pelanggan')
            ->assertSee('Budi Santoso');

        // 2. Laporan Penyewaan
        $response = $this->get(route('admin.reports.index'));
        $response->assertStatus(200)
            ->assertSee('Laporan bisnis');

        // 3. Pengaturan Akun Admin
        $response = $this->get(route('admin.settings.index'));
        $response->assertStatus(200)
            ->assertSee('Pengaturan Akun Administrator');

        $response = $this->post(route('admin.settings.update'), [
            'name' => 'Admin Retronics Utama',
            'email' => 'admin@retronics.test',
            'phone' => '081299990000',
        ]);
        $response->assertSessionHas('success');
        $admin->refresh();
        $this->assertEquals('Admin Retronics Utama', $admin->name);
    }
}
