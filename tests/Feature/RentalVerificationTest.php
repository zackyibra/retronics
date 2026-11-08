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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RentalVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'phone' => '081234567890',
            'address' => 'Jl. Pabuaran No. 10, Gunung Putri, Bogor',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '081929513780',
        ]);

        $category = Category::create([
            'name' => 'Kamera Mirrorless',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sony A7 IV',
            'slug' => 'sony-a7-iv',
            'price_per_day' => 500000,
            'stock' => 5,
            'condition' => 'Sangat Baik',
            'is_active' => true,
        ]);
    }

    public function test_adult_customer_can_submit_rental_with_ktp_and_signature(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 2,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp_asli.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Produksi Video Klip Musik',
            'emergency_contact_name' => 'Budi Santoso',
            'emergency_contact_relation' => 'Kakak Kandung',
            'emergency_contact_phone' => '081398765432',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertRedirect();

        $rental = Rental::first();
        $this->assertNotNull($rental);

        $verification = RentalVerification::where('rental_id', $rental->id)->first();
        $this->assertNotNull($verification);
        $this->assertEquals('3578012345678901', $verification->id_card_number);
        $this->assertEquals(RentalVerification::STATUS_MENUNGGU, $verification->verification_status);
        $this->assertFalse($verification->requires_stamp_duty);
        $this->assertNotNull($verification->signature_data);
    }

    public function test_rental_over_5_million_requires_stamp_duty(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 4,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 3],
            ],
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp_asli.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Shooting Film Layar Lebar',
            'emergency_contact_name' => 'Budi Santoso',
            'emergency_contact_relation' => 'Produser',
            'emergency_contact_phone' => '081398765432',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertRedirect();
        $rental = Rental::first();
        $this->assertGreaterThanOrEqual(5000000, (float) $rental->total_price);

        $verification = RentalVerification::where('rental_id', $rental->id)->first();
        $this->assertTrue($verification->requires_stamp_duty);
        $this->assertEquals(10000, (float) $verification->stamp_duty_amount);
    }

    /**
     * Pelanggan di bawah 17 tahun ditolak berdasarkan tanggal lahir.
     */
    public function test_underage_customer_is_rejected_by_birth_date(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => Carbon::now()->subYears(15)->toDateString(), // Usia 15 tahun
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp_palsu.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Tugas Sekolah',
            'emergency_contact_name' => 'Orang Tua',
            'emergency_contact_relation' => 'Ayah',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors('birth_date');
        $this->assertEquals(0, Rental::count());
    }

    /**
     * Pelanggan berusia tepat 17 tahun dapat melewati validasi usia.
     */
    public function test_customer_aged_exactly_17_passes_validation(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => Carbon::now()->subYears(17)->toDateString(), // Tepat 17 tahun
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp_asli.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Lomba Fotografi Remaja',
            'emergency_contact_name' => 'Budi Santoso',
            'emergency_contact_relation' => 'Paman',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertRedirect();
        $this->assertEquals(1, Rental::count());
    }

    /**
     * Tanggal lahir tidak valid atau di masa depan ditolak.
     */
    public function test_invalid_birth_date_is_rejected(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => Carbon::tomorrow()->toDateString(), // Masa depan
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp_asli.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Fotografi',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_relation' => 'Rekan',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors('birth_date');
        $this->assertEquals(0, Rental::count());
    }

    /**
     * Pengajuan tanpa KTP ditolak.
     */
    public function test_rental_submission_without_ktp_is_rejected(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => '2000-01-01',
            // Tanpa id_card_number dan tanpa id_card_file
            'rental_purpose' => 'Tugas Kantor',
            'emergency_contact_name' => 'Budi Santoso',
            'emergency_contact_relation' => 'Rekan',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors(['id_card_number', 'id_card_file']);
        $this->assertEquals(0, Rental::count());
    }

    /**
     * Direct request mencoba bypass menggunakan tipe customer_type 'underage' ditolak secara backend.
     */
    public function test_direct_request_with_underage_customer_type_is_rejected(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'customer_type' => 'underage',
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Bypass attempt',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_relation' => 'Rekan',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors('id_card_file');
        $this->assertEquals(0, Rental::count());
    }

    /**
     * Direct request mencoba mengirimkan berkas wali atau kartu pelajar ditolak secara backend.
     */
    public function test_direct_request_with_guardian_or_student_data_is_rejected(): void
    {
        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->customer)->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 1,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345678901',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'guardian_name' => 'Hendra Wali',
            'student_card_file' => UploadedFile::fake()->create('kartu_pelajar.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Bypass attempt with guardian',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_relation' => 'Rekan',
            'emergency_contact_phone' => '081234567890',
            'signature_data' => $fakeSignature,
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors('id_card_file');
        $this->assertEquals(0, Rental::count());
    }

    public function test_customer_and_admin_can_view_rental_agreement(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-20261101-AGR01',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 1000000,
            'status' => 'pending_payment',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_number' => '3578012345678901',
            'rental_purpose' => 'Photoshoot',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_phone' => '081234567',
            'emergency_contact_relation' => 'Teman',
            'signature_data' => 'data:image/png;base64,fake',
            'agreement_signed_at' => Carbon::now(),
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        // Customer can view their agreement
        $response = $this->actingAs($this->customer)->get(route('rentals.agreement', $rental->id));
        $response->assertOk();
        $response->assertSee('SURAT PERJANJIAN PENYEWAAN');

        // Admin can also view
        $adminResponse = $this->actingAs($this->admin)->get(route('rentals.agreement', $rental->id));
        $adminResponse->assertOk();

        // Other customer cannot view (403)
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $unauthorizedResponse = $this->actingAs($otherCustomer)->get(route('rentals.agreement', $rental->id));
        $unauthorizedResponse->assertForbidden();
    }

    public function test_admin_can_update_verification_status(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-20261101-ADM01',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 1000000,
            'status' => 'pending_payment',
        ]);

        $verification = RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        // Admin marks as approved
        $response = $this->actingAs($this->admin)->post(route('admin.rentals.verify_document', $rental->id), [
            'verification_status' => 'disetujui',
            'admin_notes' => 'KTP sah dan jelas.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('disetujui', $verification->fresh()->verification_status);

        // Admin requests revision
        $revResponse = $this->actingAs($this->admin)->post(route('admin.rentals.verify_document', $rental->id), [
            'verification_status' => 'perlu_revisi',
            'admin_notes' => 'Foto KTP terpotong bagian NIK, mohon upload ulang.',
        ]);

        $revResponse->assertRedirect();
        $this->assertEquals('perlu_revisi', $verification->fresh()->verification_status);
        $this->assertEquals('Foto KTP terpotong bagian NIK, mohon upload ulang.', $verification->fresh()->admin_notes);
    }

    public function test_admin_dashboard_and_rentals_index_display_and_filter_verification_data(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-20261101-DASH01',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 1000000,
            'status' => 'waiting_verification',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_number' => '3578019999990001',
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        // 1. Dashboard displays pending document count and verification badges
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('verif dokumen');
        $response->assertSee('Periksa Berkas');
        $response->assertSee('RET-20261101-DASH01');

        // 2. Filter rentals index by verification status
        $filterResponse = $this->actingAs($this->admin)->get(route('admin.rentals.index', [
            'verification_status' => 'menunggu',
        ]));
        $filterResponse->assertOk();
        $filterResponse->assertSee('RET-20261101-DASH01');

        // Filter for another status shouldn't show this rental
        $emptyResponse = $this->actingAs($this->admin)->get(route('admin.rentals.index', [
            'verification_status' => 'disetujui',
        ]));
        $emptyResponse->assertOk();
        $emptyResponse->assertDontSee('RET-20261101-DASH01');
    }

    /**
     * Requirement D1: Dokumen menunggu -> pembayaran tidak bisa diverifikasi.
     */
    public function test_payment_cannot_be_approved_when_documents_are_still_pending(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-01',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'waiting_verification',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('waiting_verification', $rental->fresh()->status);
    }

    /**
     * Requirement D2: Dokumen perlu revisi -> penyerahan barang ditolak.
     */
    public function test_gear_handover_is_blocked_when_documents_need_revision(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-02',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'confirmed',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_PERLU_REVISI,
            'admin_notes' => 'KTP buram',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'in_use',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $rental->fresh()->status);
    }

    /**
     * Requirement D3: Dokumen ditolak -> penyerahan barang ditolak.
     */
    public function test_gear_handover_is_blocked_when_documents_are_rejected(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-03',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'confirmed',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DITOLAK,
            'admin_notes' => 'Identitas tidak valid',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'in_use',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $rental->fresh()->status);
    }

    /**
     * Requirement D4: Dokumen disetujui -> proses dapat dilanjutkan sesuai status pembayaran.
     */
    public function test_rental_flow_can_proceed_normally_when_documents_are_approved(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-04',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'waiting_verification',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_per_day' => 250000,
            'subtotal' => 500000,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        // 1. Verifikasi pembayaran disetujui
        $verifyRes = $this->actingAs($this->admin)->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);
        $verifyRes->assertSessionHas('success');
        $this->assertEquals('confirmed', $rental->fresh()->status);

        // 2. Disiapkan
        $prepRes = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'preparing',
        ]);
        $prepRes->assertSessionHas('success');
        $this->assertEquals('preparing', $rental->fresh()->status);

        // 3. Siap diambil
        $readyRes = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'ready_pickup',
        ]);
        $readyRes->assertSessionHas('success');
        $this->assertEquals('ready_pickup', $rental->fresh()->status);

        // 4. Penyerahan barang (in_use)
        $inUseRes = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'in_use',
        ]);
        $inUseRes->assertSessionHas('success');
        $this->assertEquals('in_use', $rental->fresh()->status);
    }

    /**
     * Requirement D5: Transaksi tanpa data verifikasi -> tidak bisa menyerahkan barang.
     */
    public function test_rental_without_verification_record_cannot_hand_over_gear(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-05',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'confirmed',
        ]);

        // Tanpa data RentalVerification
        $response = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'in_use',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $rental->fresh()->status);
    }

    /**
     * Requirement D6: Pembatalan transaksi tetap berfungsi.
     */
    public function test_rental_cancellation_still_works_regardless_of_verification_status(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-06',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'waiting_verification',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
        ]);

        // 1. Admin membatalkan pesanan
        $response = $this->actingAs($this->admin)->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'cancelled',
            'admin_notes' => 'Dibatalkan atas permintaan penyewa.',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $rental->fresh()->status);

        // 2. Pelanggan membatalkan pesanan via customer route
        $rentalCustomer = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-07',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'pending_payment',
        ]);

        $cancelCustomerRes = $this->actingAs($this->customer)->post(route('rentals.cancel', $rentalCustomer->id));
        $cancelCustomerRes->assertRedirect(route('account.rentals'));
        $this->assertEquals('cancelled', $rentalCustomer->fresh()->status);
    }

    /**
     * Test admin can approve pending document and payment simultaneously.
     */
    public function test_admin_can_approve_pending_document_and_payment_simultaneously(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROT-08',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => 500000,
            'status' => 'waiting_verification',
        ]);

        $verification = RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
            'id_card_number' => '3201011234567890',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
            'approve_document' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $rental->fresh()->status);
        $this->assertEquals(RentalVerification::STATUS_DISETUJUI, $verification->fresh()->verification_status);
        $this->assertEquals($this->admin->id, $verification->fresh()->verified_by);
    }
}
