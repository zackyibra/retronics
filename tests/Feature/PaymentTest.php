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
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
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
     * Helper to create a rental booking in pending_payment state.
     */
    protected function createPendingRental(float $totalPrice = 300000): Rental
    {
        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-PAYTEST-'.uniqid(),
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'duration_days' => 3,
            'total_price' => $totalPrice,
            'status' => 'pending_payment',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_per_day' => 100000,
            'subtotal' => $totalPrice,
        ]);

        RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_number' => '3578012345670001',
            'rental_purpose' => 'Test Pembayaran',
            'emergency_contact_name' => 'Budi',
            'emergency_contact_phone' => '081234567890',
            'emergency_contact_relation' => 'Keluarga',
            'signature_data' => 'data:image/png;base64,dummy',
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
        ]);

        return $rental;
    }

    /**
     * 1. Submitting payment creates a Payment record with waiting_verification status.
     */
    public function test_submitting_payment_creates_payment_record(): void
    {
        Storage::fake('public');
        $rental = $this->createPendingRental(250000);

        $this->actingAs($this->customer);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post(route('rentals.pay', $rental->id), [
            'payment_method' => 'transfer_bca',
            'payment_proof' => $file,
            'notes' => 'Transfer via mobile banking BCA.',
        ]);

        $response->assertRedirect(route('rentals.show', $rental->id));
        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals('waiting_verification', $rental->status);
        $this->assertDatabaseHas('payments', [
            'rental_id' => $rental->id,
            'amount' => 250000,
            'payment_method' => 'transfer_bca',
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $payment = $rental->payments()->latest()->first();
        $this->assertNotNull($payment);
        $this->assertStringStartsWith('PAY-', $payment->payment_code);
        $this->assertEquals('Menunggu Verifikasi', $payment->status_label);
        $this->assertEquals('Transfer Bank BCA', $payment->payment_method_label);
    }

    /**
     * 2. Admin approving payment marks Payment as success and Rental as confirmed.
     */
    public function test_admin_approving_payment_marks_payment_success_and_rental_confirmed(): void
    {
        $rental = $this->createPendingRental(400000);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-20261027-TEST01',
            'amount' => 400000,
            'payment_method' => 'transfer_mandiri',
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $rental->update(['status' => 'waiting_verification']);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'approve',
            'admin_notes' => 'Dana telah masuk ke rekening Mandiri store.',
        ]);

        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_SUCCESS, $payment->status);
        $this->assertEquals($this->admin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);
        $this->assertEquals('Dana telah masuk ke rekening Mandiri store.', $payment->notes);
        $this->assertEquals('Berhasil', $payment->status_label);

        $rental->refresh();
        $this->assertEquals('confirmed', $rental->status);
    }

    /**
     * 3. Admin rejecting payment marks Payment as rejected and resets Rental to pending_payment.
     */
    public function test_admin_rejecting_payment_marks_payment_rejected_and_resets_rental(): void
    {
        $rental = $this->createPendingRental(150000);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-20261027-TEST02',
            'amount' => 150000,
            'payment_method' => 'qris',
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $rental->update(['status' => 'waiting_verification']);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.rentals.verify', $rental->id), [
            'action' => 'reject',
            'rejection_reason' => 'Nominal transfer kurang Rp50.000.',
        ]);

        $response->assertSessionHas('info');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_REJECTED, $payment->status);
        $this->assertEquals($this->admin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);
        $this->assertEquals('Nominal transfer kurang Rp50.000.', $payment->notes);
        $this->assertEquals('Ditolak', $payment->status_label);

        $rental->refresh();
        $this->assertEquals('pending_payment', $rental->status);
    }

    /**
     * 4. Resubmitting payment creates a new Payment record while keeping rejected record in history.
     */
    public function test_resubmitting_payment_retains_history_and_creates_new_record(): void
    {
        $rental = $this->createPendingRental(200000);

        // Previous rejected payment
        $oldPayment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-OLD-001',
            'amount' => 200000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now()->subDay(),
            'status' => Payment::STATUS_REJECTED,
            'verified_by' => $this->admin->id,
            'verified_at' => now()->subDay(),
            'notes' => 'Bukti buram.',
        ]);

        $this->actingAs($this->customer);

        // Customer resubmits payment
        $response = $this->post(route('rentals.pay', $rental->id), [
            'payment_method' => 'transfer_mandiri',
            'notes' => 'Upload ulang bukti jelas.',
        ]);

        $response->assertSessionHas('success');

        $rental->refresh();
        $this->assertEquals('waiting_verification', $rental->status);
        $this->assertEquals(2, $rental->payments()->count());

        $newPayment = $rental->payments()->latest('id')->first();
        $this->assertNotEquals($oldPayment->id, $newPayment->id);
        $this->assertEquals(Payment::STATUS_WAITING_VERIFICATION, $newPayment->status);
        $this->assertEquals('transfer_mandiri', $newPayment->payment_method);

        // Old payment remains rejected
        $oldPayment->refresh();
        $this->assertEquals(Payment::STATUS_REJECTED, $oldPayment->status);
    }

    /**
     * 5. Admin can view payments index and filter by status.
     */
    public function test_admin_can_view_payments_index_page(): void
    {
        $rental = $this->createPendingRental();

        Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-INDEX-001',
            'amount' => 300000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.payments.index'));
        $response->assertStatus(200)
            ->assertSee('Kelola Pembayaran')
            ->assertSee('PAY-INDEX-001')
            ->assertSee('Menunggu Verifikasi');
    }

    /**
     * 6. Admin can verify payment directly from payments management route.
     */
    public function test_admin_can_verify_payment_directly(): void
    {
        $rental = $this->createPendingRental(350000);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-DIRECT-001',
            'amount' => 350000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.payments.verify', $payment->id), [
            'action' => 'approve',
            'notes' => 'Disetujui dari pilar pembayaran.',
        ]);

        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_SUCCESS, $payment->status);
        $this->assertEquals($this->admin->id, $payment->verified_by);

        $rental->refresh();
        $this->assertEquals('confirmed', $rental->status);
    }

    /**
     * 7. Verify all 10 entity relationships specified in the requirements.
     */
    public function test_all_ten_entity_relationships_exist_and_function(): void
    {
        $rental = $this->createPendingRental(200000);

        // 1. Users relations
        $this->assertInstanceOf(HasMany::class, $this->customer->rentals());
        $this->assertInstanceOf(HasMany::class, $this->customer->wishlists());
        $this->assertInstanceOf(HasMany::class, $this->customer->reviews());
        $this->assertInstanceOf(HasMany::class, $this->admin->assessedFines());
        $this->assertInstanceOf(HasMany::class, $this->admin->verifiedPayments());

        // 2. Categories relations
        $category = Category::first();
        $this->assertInstanceOf(HasMany::class, $category->products());
        $this->assertInstanceOf(HasMany::class, $category->promotions());

        // 3. Products relations
        $this->assertInstanceOf(BelongsTo::class, $this->product->category());
        $this->assertInstanceOf(HasMany::class, $this->product->rentalItems());
        $this->assertInstanceOf(HasMany::class, $this->product->wishlists());
        $this->assertInstanceOf(HasMany::class, $this->product->reviews());

        // 4. Rentals relations
        $this->assertInstanceOf(BelongsTo::class, $rental->user());
        $this->assertInstanceOf(HasMany::class, $rental->items());
        $this->assertInstanceOf(HasMany::class, $rental->reviews());
        $this->assertInstanceOf(HasMany::class, $rental->fines());
        $this->assertInstanceOf(HasMany::class, $rental->payments());
        $this->assertInstanceOf(HasOne::class, $rental->latestPayment());

        // 5. Rental Items relations
        $item = $rental->items->first();
        $this->assertInstanceOf(BelongsTo::class, $item->rental());
        $this->assertInstanceOf(BelongsTo::class, $item->product());
        $this->assertInstanceOf(HasMany::class, $item->fines());

        // 6. Wishlists relations
        $wishlist = Wishlist::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);
        $this->assertInstanceOf(BelongsTo::class, $wishlist->user());
        $this->assertInstanceOf(BelongsTo::class, $wishlist->product());

        // 7. Reviews relations
        $rental->update(['status' => 'completed']);
        $review = Review::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'rental_id' => $rental->id,
            'rating' => 5,
            'comment' => 'Kamera sangat tajam dan kondisi sangat terawat.',
        ]);
        $this->assertInstanceOf(BelongsTo::class, $review->user());
        $this->assertInstanceOf(BelongsTo::class, $review->product());
        $this->assertInstanceOf(BelongsTo::class, $review->rental());

        // 8. Fines relations
        $fine = Fine::create([
            'rental_id' => $rental->id,
            'rental_item_id' => $item->id,
            'type' => Fine::TYPE_LATE,
            'amount' => 50000,
            'days_late' => 1,
            'rate_per_day' => 50000,
            'assessed_by' => $this->admin->id,
        ]);
        $this->assertInstanceOf(BelongsTo::class, $fine->rental());
        $this->assertInstanceOf(BelongsTo::class, $fine->rentalItem());
        $this->assertInstanceOf(BelongsTo::class, $fine->assessor());

        // 9. Promotions relations
        $promo = Promotion::first();
        $this->assertInstanceOf(BelongsTo::class, $promo->category());

        // 10. Payments relations
        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-REL-001',
            'amount' => 200000,
            'payment_method' => 'transfer_bca',
            'payment_date' => now(),
            'status' => Payment::STATUS_SUCCESS,
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
        ]);
        $this->assertInstanceOf(BelongsTo::class, $payment->rental());
        $this->assertInstanceOf(BelongsTo::class, $payment->verifiedBy());
        $this->assertInstanceOf(BelongsTo::class, $payment->verifier());
        $this->assertEquals($this->admin->id, $payment->verifiedBy->id);
    }
}
