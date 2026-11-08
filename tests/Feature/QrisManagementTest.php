<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\QrisSetting;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrisManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '081299990001',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'phone' => '081299990002',
        ]);
    }

    public function test_customer_cannot_access_admin_qris_page(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.qris.index'));
        $response->assertRedirect(route('account.index'));
    }

    public function test_admin_can_access_qris_management_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.qris.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan QRIS Pembayaran');
        $response->assertSee('Unggah & Pasang QRIS Baru', false);
    }

    public function test_admin_can_upload_and_activate_new_qris(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('qris_warpit.png', 400, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.qris.store'), [
            'merchant_name' => 'Warpit Retronics',
            'nmid' => 'ID1026549194243',
            'account_notes' => 'Store Cikeas Udik, Gunung Putri',
            'image' => $file,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.qris.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('qris_settings', [
            'merchant_name' => 'Warpit Retronics',
            'nmid' => 'ID1026549194243',
            'is_active' => true,
        ]);
    }

    public function test_rental_page_displays_active_qris_details(): void
    {
        $qris = QrisSetting::create([
            'merchant_name' => 'Warpit Retronics',
            'nmid' => 'ID1026549194243',
            'image_path' => 'qris/qris_dana_warpit.jpg',
            'is_active' => true,
            'uploaded_by' => $this->admin->id,
        ]);

        $category = Category::create([
            'name' => 'Lensa Test',
            'slug' => 'lensa-qris-test',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Canon EF 50mm STM',
            'slug' => 'gear-qris-test',
            'price_per_day' => 50000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $rental = Rental::create([
            'rental_code' => 'RET-QRIS-001',
            'user_id' => $this->customer->id,
            'start_date' => Carbon::now()->addDay(),
            'end_date' => Carbon::now()->addDays(2),
            'duration_days' => 1,
            'total_price' => 50000,
            'status' => 'pending_payment',
        ]);

        $response = $this->actingAs($this->customer)->get(route('rentals.show', $rental->id));

        $response->assertOk();
        $response->assertSee($qris->merchant_name);
        $response->assertSee($qris->nmid);
        $response->assertSee('Perbesar / Scan');
    }
}
