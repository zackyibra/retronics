<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalPrintFormTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    private Rental $rental;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '081929513780',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Doni Kusuma',
            'phone' => '081234567899',
            'address' => 'Jl. Pabuaran No. 12, Gunung Putri, Bogor',
        ]);

        $category = Category::create([
            'name' => 'Kamera Bioskop',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sony FX3 Cinema Line',
            'slug' => 'sony-fx3-cinema-line',
            'price_per_day' => 750000,
            'stock' => 3,
            'condition' => 'Sangat Baik',
            'is_active' => true,
        ]);

        $this->rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PRINT-001',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'duration_days' => 3,
            'total_price' => 1912500, // 3 * 750.000 = 2.250.000 - 15% durasi = 1.912.500
            'status' => 'confirmed',
        ]);

        RentalItem::create([
            'rental_id' => $this->rental->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price_per_day' => 750000,
            'subtotal' => 2250000,
        ]);
    }

    /**
     * Admin dapat membuka halaman cetak formulir A4 dengan data lengkap dan NIK disamarkan.
     */
    public function test_admin_can_view_printable_rental_form_with_masked_nik(): void
    {
        RentalVerification::create([
            'rental_id' => $this->rental->id,
            'user_id' => $this->customer->id,
            'birth_date' => '1998-05-14',
            'id_card_number' => '3578011405980003',
            'rental_purpose' => 'Shooting Video Promosi Komersial',
            'emergency_contact_name' => 'Siti Kusuma',
            'emergency_contact_relation' => 'Istri',
            'emergency_contact_phone' => '081399887766',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agreement_signed_at' => Carbon::now(),
            'agreement_ip_address' => '127.0.0.1',
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rentals.print', $this->rental->id));

        $response->assertOk();
        // 1. Identitas Retronics & Judul
        $response->assertSee('RETRONICS INDONESIA');
        $response->assertSee('FORMULIR PENYEWAAN & BUKTI SERAH TERIMA BARANG', false);
        $response->assertSee('RET-PRINT-001');

        // 2. Data Penyewa
        $response->assertSee('Doni Kusuma');
        // NIK harus disamarkan sebagian (tidak full): 3578********0003
        $response->assertSee('3578********0003');
        $response->assertDontSee('3578011405980003');

        // 3. Data Barang & Durasi
        $response->assertSee('Sony FX3 Cinema Line');
        $response->assertSee('3 Hari Kalender');

        // 4. Tanda Tangan Digital Pelanggan
        $response->assertSee('Ditandatangani secara elektronik');

        // 5. Ruang tanda tangan admin manual (bukan otomatis)
        $response->assertSee('Ruang tanda tangan basah & stempel toko', false);
        $response->assertSee('( .................................................... )');
    }

    /**
     * Jika tanda tangan pelanggan belum ada, tampilkan status belum ditandatangani dan jangan buat tanda tangan palsu.
     */
    public function test_form_shows_unsigned_status_when_customer_signature_is_missing(): void
    {
        RentalVerification::create([
            'rental_id' => $this->rental->id,
            'user_id' => $this->customer->id,
            'birth_date' => '1998-05-14',
            'id_card_number' => '3578011405980003',
            'rental_purpose' => 'Pribadi',
            'emergency_contact_name' => 'Siti',
            'emergency_contact_relation' => 'Istri',
            'emergency_contact_phone' => '081399887766',
            'signature_data' => null, // Belum ada tanda tangan
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rentals.print', $this->rental->id));

        $response->assertOk();
        $response->assertSee('[ DOKUMEN BELUM DITANDATANGANI SECARA ELEKTRONIK ]');
        $response->assertDontSee('Ditandatangani secara elektronik');
    }

    /**
     * Non-admin atau user biasa tidak berhak membuka halaman cetak formulir admin.
     */
    public function test_unauthorized_user_cannot_access_admin_print_form(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.rentals.print', $this->rental->id));
        $response->assertRedirect(route('account.index'));

        auth()->logout();

        $guestResponse = $this->get(route('admin.rentals.print', $this->rental->id));
        $guestResponse->assertRedirect(route('admin.login'));
    }
}
