<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessLogicAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_test@retronics.test',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer_test@retronics.test',
            'phone' => '081234567890',
        ]);

        $category = Category::create([
            'name' => 'Kamera',
            'slug' => 'kamera',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fujifilm X-T4',
            'slug' => 'fujifilm-x-t4',
            'price_per_day' => 200000,
            'stock' => 2,
            'is_active' => true,
            'condition' => 'Baik / Prima',
        ]);
    }

    /**
     * B6: Test duplicate products in same booking request are consolidated and validated against total stock.
     */
    public function test_duplicate_products_in_same_booking_are_consolidated_and_checked_against_stock(): void
    {
        $this->actingAs($this->customer);

        // Product only has stock = 2. Submitting product twice with quantity = 2 total = 4 (> 2)
        $response = $this->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 2,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 2],
                ['id' => $this->product->id, 'quantity' => 2],
            ],
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345670001',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Event Video',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Keluarga',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors(['products']);
        $this->assertEquals(0, Rental::count());
    }

    /**
     * B1: Test admin cannot verify payment if stock is insufficient when approving.
     */
    public function test_admin_cannot_verify_payment_if_stock_is_insufficient(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-STOCK-TEST-01',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 400000,
            'status' => 'waiting_verification',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_per_day' => 200000,
            'subtotal' => 400000,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        // Manually drop product stock to 1 (less than needed quantity 2)
        $this->product->update(['stock' => 1]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $rental->refresh();
        $this->assertEquals('waiting_verification', $rental->status);
        $this->assertEquals(1, $this->product->fresh()->stock);
    }

    /**
     * B3 & B5: Test admin cancelling confirmed rental restores stock and promo quota.
     */
    public function test_admin_cancelling_confirmed_rental_restores_stock_and_promo_quota(): void
    {
        $promo = Promotion::create([
            'code' => 'TESTRESTORE',
            'name' => 'Restore Promo',
            'type' => 'fixed',
            'discount_value' => 50000,
            'quota_total' => 10,
            'quota_used' => 5,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-CANCEL-TEST',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 350000,
            'promo_code' => 'TESTRESTORE',
            'discount_amount' => 50000,
            'status' => 'confirmed',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_per_day' => 200000,
            'subtotal' => 400000,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $initialStock = $this->product->stock;

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'cancelled',
            'admin_notes' => 'Pembatalan karena customer berhalangan.',
        ]);

        $response->assertSessionHas('success');
        $rental->refresh();
        $this->assertEquals('cancelled', $rental->status);
        $this->assertEquals($initialStock + 1, $this->product->fresh()->stock);
        $this->assertEquals(4, $promo->fresh()->quota_used);
    }

    /**
     * B3: Test admin cannot cancel or reject a completed rental.
     */
    public function test_admin_cannot_cancel_or_reject_completed_rental(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-COMPLETED-01',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'duration_days' => 3,
            'total_price' => 600000,
            'status' => 'completed',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.update_status', $rental->id), [
            'status' => 'cancelled',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('completed', $rental->fresh()->status);
    }

    /**
     * B5: Test user cannot reuse same promo code twice on active bookings.
     */
    public function test_user_cannot_reuse_promo_code_twice(): void
    {
        Promotion::create([
            'code' => 'ONETIMEONLY',
            'name' => 'One Time Promo',
            'type' => 'percentage',
            'discount_value' => 10,
            'quota_total' => 10,
            'quota_used' => 0,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PROMO-PREV',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 360000,
            'promo_code' => 'ONETIMEONLY',
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->customer);

        $response = $this->post(route('rentals.store'), [
            'start_date' => Carbon::tomorrow()->toDateString(),
            'duration_days' => 2,
            'products' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
            'promo_code' => 'ONETIMEONLY',
            'birth_date' => '2000-01-01',
            'id_card_number' => '3578012345670001',
            'id_card_file' => UploadedFile::fake()->create('ktp.jpg', 500, 'image/jpeg'),
            'rental_purpose' => 'Event Video',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Keluarga',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'agree_terms' => 1,
        ]);

        $response->assertSessionHasErrors(['promo_code']);
    }

    /**
     * B8: Test admin cannot mark an already paid fine as paid again.
     */
    public function test_admin_cannot_mark_already_paid_fine_as_paid_again(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-FINE-TEST',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'duration_days' => 3,
            'total_price' => 600000,
            'status' => 'completed',
        ]);

        $fine = Fine::create([
            'rental_id' => $rental->id,
            'type' => Fine::TYPE_LATE,
            'amount' => 50000,
            'days_late' => 1,
            'rate_per_day' => 50000,
            'reason' => 'Terlambat 1 hari',
            'assessed_by' => $this->admin->id,
            'is_paid' => true,
            'paid_at' => now(),
            'payment_reference' => 'Lunas awal',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.returns.pay_fine', $fine->id));

        $response->assertSessionHas('error');
    }

    /**
     * B8: Test admin cannot update document verification on completed rental.
     */
    public function test_admin_cannot_update_document_verification_on_completed_rental(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-DOC-COMPLETED',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'duration_days' => 3,
            'total_price' => 600000,
            'status' => 'completed',
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.verify_document', $rental->id), [
            'verification_status' => 'ditolak',
            'admin_notes' => 'Tolak terlambat.',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * C5: Test resubmitting document deletes old file and uses customer notes.
     */
    public function test_resubmit_document_deletes_old_file_and_updates_notes(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-RESUBMIT',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 400000,
            'status' => 'pending_payment',
        ]);

        $fakeOldPath = 'private/verifications/ktp/old_ktp.jpg';
        Storage::disk('local')->put($fakeOldPath, 'dummy old content');

        $verification = RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_file' => $fakeOldPath,
            'verification_status' => RentalVerification::STATUS_PERLU_REVISI,
            'admin_notes' => 'Foto KTP buram.',
        ]);

        $this->actingAs($this->customer);

        $response = $this->post(route('rentals.verification.resubmit', $rental->id), [
            'id_card_file' => UploadedFile::fake()->create('ktp_baru.jpg', 600, 'image/jpeg'),
            'notes' => 'Ini foto KTP resolusi tinggi baru.',
        ]);

        $response->assertSessionHas('success');
        Storage::disk('local')->assertMissing($fakeOldPath);

        $verification->refresh();
        $this->assertEquals(RentalVerification::STATUS_MENUNGGU, $verification->verification_status);
        $this->assertStringContainsString('Catatan revisi penyewa: Ini foto KTP resolusi tinggi baru.', $verification->admin_notes);
    }

    /**
     * C3: Test logout rejects GET requests.
     */
    public function test_logout_rejects_get_request(): void
    {
        $this->actingAs($this->customer);

        $response = $this->get('/logout');
        $response->assertStatus(405); // Method Not Allowed
    }

    /**
     * A3: Test submitting payment on confirmed, in_use, or completed rental is forbidden.
     */
    public function test_submitting_payment_on_confirmed_or_in_use_rental_is_forbidden(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-SUBMIT-CONFIRMED',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 400000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->customer);

        $response = $this->post(route('rentals.pay', $rental->id), [
            'payment_method' => 'transfer_bca',
        ]);

        $response->assertRedirect(route('rentals.show', $rental->id));
        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $rental->fresh()->status);
    }

    /**
     * A4: Test double click / double approval does not decrement stock twice.
     */
    public function test_double_verifying_payment_does_not_decrement_stock_twice(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-DOUBLE-APPROVE',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 200000,
            'status' => 'waiting_verification',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_per_day' => 200000,
            'subtotal' => 200000,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $initialStock = $this->product->stock; // 2

        $this->actingAs($this->admin);

        // First approval
        $firstResponse = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);
        $firstResponse->assertSessionHas('success');
        $this->assertEquals('confirmed', $rental->fresh()->status);
        $this->assertEquals($initialStock - 1, $this->product->fresh()->stock); // 1

        // Second approval (double click or re-approval attempt)
        $secondResponse = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
        ]);
        $secondResponse->assertSessionHas('error');
        $this->assertEquals($initialStock - 1, $this->product->fresh()->stock); // Still 1, NOT 0!
    }

    /**
     * A4: Test double click / double approval direct does not decrement stock twice.
     */
    public function test_double_verifying_payment_direct_does_not_decrement_stock_twice(): void
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-DOUBLE-DIRECT',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 200000,
            'status' => 'waiting_verification',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_per_day' => 200000,
            'subtotal' => 200000,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-DIRECT-TEST',
            'amount' => 200000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $initialStock = $this->product->stock; // 2

        $this->actingAs($this->admin);

        // First approval direct
        $firstResponse = $this->post(route('admin.payments.verify', $payment->id), [
            'action' => 'approve',
        ]);
        $firstResponse->assertSessionHas('success');
        $this->assertEquals('confirmed', $rental->fresh()->status);
        $this->assertEquals($initialStock - 1, $this->product->fresh()->stock); // 1

        // Second approval direct attempt
        $secondResponse = $this->post(route('admin.payments.verify', $payment->id), [
            'action' => 'approve',
        ]);
        $secondResponse->assertSessionHas('error');
        $this->assertEquals($initialStock - 1, $this->product->fresh()->stock); // Still 1!
    }

    /**
     * B7: Test admin report does not display hardcoded dummy numbers.
     */
    public function test_admin_reports_displays_real_calculated_data_and_no_dummy_numbers(): void
    {
        $this->actingAs($this->admin);

        // When database has no completed or verified rentals
        $response = $this->get(route('admin.reports.index'));
        $response->assertStatus(200);

        // Must NOT contain the old hardcoded mockup strings
        $response->assertDontSee('12.840.000');
        $response->assertDontSee('480.000');
        $response->assertDontSee('150.000');
        $response->assertDontSee('200.000');
        $response->assertDontSee('20 transaksi - rata-rata');

        // Must display actual 0
        $response->assertSee('Rp0');

        // Now create a real rental with verified payment and fine
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-REPORT-TEST-1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'promo_code' => 'DISC50',
            'subtotal' => 350000,
            'discount_amount' => 50000,
            'total_price' => 300000,
            'status' => 'confirmed',
            'fine_amount' => 75000,
            'fine_paid' => true,
        ]);

        Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-REP-1',
            'amount' => 300000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now(),
            'status' => Payment::STATUS_SUCCESS,
        ]);

        Fine::create([
            'rental_id' => $rental->id,
            'type' => Fine::TYPE_LATE,
            'amount' => 75000,
            'days_late' => 1,
            'rate_per_day' => 75000,
            'reason' => 'Keterlambatan 1 hari',
            'assessed_by' => $this->admin->id,
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        $responseWithData = $this->get(route('admin.reports.index'));
        $responseWithData->assertStatus(200);

        // Omset should show Rp300.000
        $responseWithData->assertSee('Rp300.000');
        // Voucher discount should show Rp50.000
        $responseWithData->assertSee('Rp50.000');
        // Fine paid should show Rp75.000
        $responseWithData->assertSee('Rp75.000');

        // CSV export should stream successfully
        $csvResponse = $this->get(route('admin.reports.index', ['export' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('RET-REPORT-TEST-1', $csvResponse->streamedContent());
    }

    public function test_products_in_maintenance_cannot_be_rented_or_displayed_in_catalog(): void
    {
        $maintenanceProduct = Product::create([
            'category_id' => $this->product->category_id,
            'name' => 'Lensa Rusak Sedang Servis',
            'slug' => 'lensa-rusak-sedang-servis',
            'description' => 'Dalam proses perbaikan.',
            'price_per_day' => 100000,
            'stock' => 2,
            'condition' => 'Dalam Perawatan Teknis',
            'is_active' => true,
        ]);

        // 1. Katalog tidak menampilkan produk dalam perawatan
        $catalogResponse = $this->get(route('products.index'));
        $catalogResponse->assertOk();
        $catalogResponse->assertDontSee('Lensa Rusak Sedang Servis');

        // 2. Tidak dapat ditambahkan ke keranjang
        $this->actingAs($this->customer);
        $cartResponse = $this->post(route('cart.add'), [
            'product_id' => $maintenanceProduct->id,
            'quantity' => 1,
        ]);
        $cartResponse->assertSessionHas('error');

        // 3. Form booking tidak menampilkan produk dalam perawatan
        session()->flush();
        $bookingResponse = $this->get(route('rentals.create'));
        $bookingResponse->assertOk();
        $bookingResponse->assertDontSee('Lensa Rusak Sedang Servis');
    }
}
