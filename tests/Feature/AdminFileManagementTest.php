<?php

namespace Tests\Feature;

use App\Models\AdminFileAuditLog;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\QrisSetting;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminFileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');

        $this->admin = User::create([
            'name' => 'Admin Retronics',
            'email' => 'admin@retronics.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'name' => 'Customer Retronics',
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Kamera',
            'slug' => 'kamera',
        ]);
    }

    protected function createTestProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'category_id' => $this->category->id,
            'name' => 'Test Camera Pro '.uniqid(),
            'slug' => 'test-camera-'.Str::random(6),
            'price_per_day' => 150000,
            'stock' => 3,
            'condition' => 'Mulus 99%',
            'is_active' => true,
        ], $attributes));
    }

    protected function createTestRental(array $attributes = []): Rental
    {
        return Rental::create(array_merge([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-FILE-'.strtoupper(Str::random(6)),
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 150000,
            'status' => 'waiting_verification',
        ], $attributes));
    }

    public function test_admin_can_view_audit_logs_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.files.audit_logs'));

        $response->assertStatus(200);
        $response->assertSee('Audit Log Berkas');
    }

    public function test_customer_cannot_access_audit_logs_or_delete_files(): void
    {
        $product = $this->createTestProduct([
            'image' => 'products/sample.jpg',
        ]);

        $response = $this->actingAs($this->customer)->delete(route('admin.products.delete_photo', $product->id));
        $response->assertRedirect(route('account.index'));
        $response->assertSessionHas('error');

        $responseLogs = $this->actingAs($this->customer)->get(route('admin.files.audit_logs'));
        $responseLogs->assertRedirect(route('account.index'));
        $responseLogs->assertSessionHas('error');
    }

    public function test_delete_payment_proof_preserves_payment_and_rental_records(): void
    {
        $proofFile = UploadedFile::fake()->image('bukti_transfer.jpg');
        $storedPath = $proofFile->store('payments', 'public');

        $rental = $this->createTestRental([
            'payment_proof' => $storedPath,
            'total_price' => 150000,
            'status' => 'waiting_verification',
        ]);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-TEST001',
            'payment_method' => 'qris',
            'amount' => 150000,
            'payment_proof' => $storedPath,
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        $response = $this->actingAs($this->admin)->delete(route('admin.payments.delete_proof', $payment->id), [
            'reason' => 'Bukti buram dan tidak terbaca',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // File fisik terhapus dari storage
        Storage::disk('public')->assertMissing($storedPath);

        // Data pembayaran tetap tersimpan aman di database
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_code' => 'PAY-TEST001',
            'amount' => 150000,
            'status' => Payment::STATUS_WAITING_VERIFICATION,
            'payment_proof' => null,
        ]);

        // Data rental tetap utuh
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'payment_proof' => null,
        ]);

        // Audit log tercatat
        $this->assertDatabaseHas('admin_file_audit_logs', [
            'admin_id' => $this->admin->id,
            'file_type' => 'payment_proof',
            'related_model_type' => Payment::class,
            'related_model_id' => $payment->id,
            'reason' => 'Bukti buram dan tidak terbaca',
        ]);
    }

    public function test_admin_can_download_payment_proof(): void
    {
        $rental = $this->createTestRental();
        $proofFile = UploadedFile::fake()->image('bukti_transfer.jpg');
        $storedPath = $proofFile->store('payments', 'public');

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-DOWNLOAD',
            'payment_method' => 'bca_manual',
            'amount' => 70000,
            'payment_proof' => $storedPath,
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.download_proof', $payment->id));

        $response->assertStatus(200);
        $this->assertStringContainsString('Bukti-Bayar-PAY-DOWNLOAD', (string) $response->headers->get('content-disposition'));
    }

    public function test_admin_can_replace_payment_proof(): void
    {
        $rental = $this->createTestRental();
        $oldFile = UploadedFile::fake()->image('old_proof.jpg');
        $oldPath = $oldFile->store('payments', 'public');

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => 'PAY-REPLACE',
            'payment_method' => 'qris',
            'amount' => 50000,
            'payment_proof' => $oldPath,
            'payment_date' => now(),
            'status' => Payment::STATUS_WAITING_VERIFICATION,
        ]);

        $newFile = UploadedFile::fake()->image('new_proof.png');

        $response = $this->actingAs($this->admin)->post(route('admin.payments.replace_proof', $payment->id), [
            'payment_proof' => $newFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('public')->assertMissing($oldPath);
        $payment->refresh();
        $this->assertNotNull($payment->payment_proof);
        Storage::disk('public')->assertExists($payment->payment_proof);
    }

    public function test_delete_product_image_preserves_product_data(): void
    {
        $imageFile = UploadedFile::fake()->image('canon_r50.jpg');
        $storedPath = $imageFile->store('products', 'public');

        $product = $this->createTestProduct([
            'name' => 'Canon EOS R50 Mirrorless',
            'image' => $storedPath,
            'price_per_day' => 150000,
            'stock' => 3,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.delete_photo', $product->id), [
            'reason' => 'Pergantian foto resmi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // File terhapus
        Storage::disk('public')->assertMissing($storedPath);

        // Produk tetap ada dengan image = null
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Canon EOS R50 Mirrorless',
            'price_per_day' => 150000,
            'stock' => 3,
            'image' => null,
        ]);

        // Audit log tercatat
        $this->assertDatabaseHas('admin_file_audit_logs', [
            'admin_id' => $this->admin->id,
            'file_type' => 'product_image',
            'related_model_type' => Product::class,
            'related_model_id' => $product->id,
        ]);
    }

    public function test_delete_promotion_image_preserves_promotion_code_and_discount(): void
    {
        $bannerFile = UploadedFile::fake()->image('promo_banner.jpg');
        $storedPath = $bannerFile->store('promotions', 'public');

        $promo = Promotion::create([
            'code' => 'RETROKREATIF',
            'name' => 'Diskon Kreator 20%',
            'type' => 'percentage',
            'discount_value' => 20,
            'min_transaction' => 100000,
            'quota_total' => 50,
            'quota_used' => 2,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(10),
            'image' => $storedPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.promotions.delete_image', $promo->id), [
            'reason' => 'Banner kedaluwarsa',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('public')->assertMissing($storedPath);

        // Promo tetap ada dan aktif
        $this->assertDatabaseHas('promotions', [
            'id' => $promo->id,
            'code' => 'RETROKREATIF',
            'discount_value' => 20,
            'is_active' => true,
            'image' => null,
        ]);

        $this->assertDatabaseHas('admin_file_audit_logs', [
            'file_type' => 'promotion_image',
            'related_model_type' => Promotion::class,
            'related_model_id' => $promo->id,
        ]);
    }

    public function test_active_qris_cannot_be_deleted_directly(): void
    {
        $qrisFile = UploadedFile::fake()->image('active_qris.jpg');
        $storedPath = $qrisFile->store('qris', 'public');

        $activeQris = QrisSetting::create([
            'merchant_name' => 'Warpit Retronics',
            'nmid' => 'ID1026549194243',
            'image_path' => $storedPath,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.qris.destroy', $activeQris->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // File dan record QRIS aktif tidak boleh terhapus
        Storage::disk('public')->assertExists($storedPath);
        $this->assertDatabaseHas('qris_settings', [
            'id' => $activeQris->id,
            'is_active' => true,
        ]);
    }

    public function test_inactive_qris_can_be_deleted(): void
    {
        $qrisFile = UploadedFile::fake()->image('inactive_qris.jpg');
        $storedPath = $qrisFile->store('qris', 'public');

        $inactiveQris = QrisSetting::create([
            'merchant_name' => 'Arsip QRIS Lama',
            'nmid' => 'ID99999999999',
            'image_path' => $storedPath,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.qris.destroy', $inactiveQris->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('public')->assertMissing($storedPath);
        $this->assertDatabaseMissing('qris_settings', [
            'id' => $inactiveQris->id,
        ]);

        $this->assertDatabaseHas('admin_file_audit_logs', [
            'file_type' => 'qris_image',
            'related_model_type' => QrisSetting::class,
            'related_model_id' => $inactiveQris->id,
        ]);
    }

    public function test_ktp_document_deletion_is_blocked_when_rental_or_verification_is_active(): void
    {
        $ktpFile = UploadedFile::fake()->image('ktp_asli.jpg');
        $storedPath = $ktpFile->store('verifications/id_cards', 'local');

        $rental = $this->createTestRental([
            'status' => 'waiting_verification',
        ]);

        $verification = RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_number' => '3201012345678901',
            'id_card_file' => $storedPath,
            'verification_status' => RentalVerification::STATUS_MENUNGGU,
            'full_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'domicile_address' => 'Gunung Putri, Bogor',
            'rental_purpose' => 'Event Dokumentasi',
        ]);

        // Coba hapus saat sewa/verifikasi masih aktif
        $response = $this->actingAs($this->admin)->delete(route('admin.rentals.delete_document', [
            'id' => $rental->id,
            'type' => 'id_card',
        ]), [
            'reason' => 'Coba hapus KTP aktif',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // File dan record KTP tetap aman
        Storage::disk('local')->assertExists($storedPath);
        $this->assertDatabaseHas('rental_verifications', [
            'id' => $verification->id,
            'id_card_file' => $storedPath,
        ]);
    }

    public function test_ktp_document_can_be_deleted_when_rental_is_completed_with_required_reason(): void
    {
        $ktpFile = UploadedFile::fake()->image('ktp_selesai.jpg');
        $storedPath = $ktpFile->store('verifications/id_cards', 'local');

        $rental = $this->createTestRental([
            'status' => 'completed',
        ]);

        $verification = RentalVerification::create([
            'rental_id' => $rental->id,
            'user_id' => $this->customer->id,
            'id_card_number' => '3201012345678901',
            'id_card_file' => $storedPath,
            'verification_status' => RentalVerification::STATUS_DISETUJUI,
            'full_name' => 'Budi Santoso Selesai',
            'phone' => '081234567890',
            'domicile_address' => 'Gunung Putri, Bogor',
            'rental_purpose' => 'Event Selesai',
        ]);

        // Validasi gagal jika tanpa reason
        $responseNoReason = $this->actingAs($this->admin)->delete(route('admin.rentals.delete_document', [
            'id' => $rental->id,
            'type' => 'id_card',
        ]), []);
        $responseNoReason->assertSessionHasErrors('reason');

        // Berhasil jika menyertakan alasan yang sah
        $response = $this->actingAs($this->admin)->delete(route('admin.rentals.delete_document', [
            'id' => $rental->id,
            'type' => 'id_card',
        ]), [
            'reason' => 'Pembersihan data pribadi setelah transaksi sewa selesai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('local')->assertMissing($storedPath);

        // Data penyewa dan nomor NIK tetap ada di database, hanya file yang di-null-kan
        $this->assertDatabaseHas('rental_verifications', [
            'id' => $verification->id,
            'id_card_number' => '3201012345678901',
            'id_card_file' => null,
        ]);

        // Tercatat di audit log
        $this->assertDatabaseHas('admin_file_audit_logs', [
            'admin_id' => $this->admin->id,
            'file_type' => 'id_card_file',
            'related_model_type' => RentalVerification::class,
            'related_model_id' => $verification->id,
            'reason' => 'Pembersihan data pribadi setelah transaksi sewa selesai',
        ]);
    }

    public function test_delete_fine_photo_preserves_fine_amount_and_reason(): void
    {
        $damagePhoto = UploadedFile::fake()->image('lensa_pecah.jpg');
        $storedPath = $damagePhoto->store('fines', 'public');

        $rental = $this->createTestRental([
            'status' => 'completed',
        ]);

        $fine = Fine::create([
            'rental_id' => $rental->id,
            'type' => Fine::TYPE_DAMAGE,
            'amount' => 250000,
            'reason' => 'Goresan optik lensa bagian depan',
            'photo' => $storedPath,
            'is_paid' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.returns.delete_fine_photo', $fine->id), [
            'reason' => 'Lampiran foto sudah tidak diperlukan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('public')->assertMissing($storedPath);

        // Denda tetap ada dengan foto null
        $this->assertDatabaseHas('fines', [
            'id' => $fine->id,
            'amount' => 250000,
            'reason' => 'Goresan optik lensa bagian depan',
            'is_paid' => true,
            'photo' => null,
        ]);

        $this->assertDatabaseHas('admin_file_audit_logs', [
            'file_type' => 'fine_photo',
            'related_model_type' => Fine::class,
            'related_model_id' => $fine->id,
        ]);
    }

    public function test_multi_reference_file_is_not_deleted_from_disk_if_used_by_another_record(): void
    {
        $sharedPhoto = UploadedFile::fake()->image('shared_product.jpg');
        $storedPath = $sharedPhoto->store('products', 'public');

        $productA = $this->createTestProduct([
            'name' => 'Kamera A',
            'image' => $storedPath,
        ]);

        $productB = $this->createTestProduct([
            'name' => 'Kamera B',
            'image' => $storedPath,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        // Hapus foto dari produk A
        $response = $this->actingAs($this->admin)->delete(route('admin.products.delete_photo', $productA->id), [
            'reason' => 'Hapus foto produk A',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // File fisik di storage HARUS TETAP ADA karena Produk B masih menggunakannya
        Storage::disk('public')->assertExists($storedPath);

        // Produk A image null
        $this->assertDatabaseHas('products', [
            'id' => $productA->id,
            'image' => null,
        ]);

        // Produk B image tetap terjaga
        $this->assertDatabaseHas('products', [
            'id' => $productB->id,
            'image' => $storedPath,
        ]);
    }

    public function test_audit_logs_can_be_filtered_by_file_type_and_searched(): void
    {
        AdminFileAuditLog::create([
            'admin_id' => $this->admin->id,
            'file_type' => 'payment_proof',
            'file_path' => 'payments/bukti_a.jpg',
            'file_name' => 'bukti_a.jpg',
            'reference_code' => 'PAY-SEARCH-TEST',
            'reason' => 'Bukti mutasi buram',
            'deleted_at' => now(),
        ]);

        AdminFileAuditLog::create([
            'admin_id' => $this->admin->id,
            'file_type' => 'product_image',
            'file_path' => 'products/gear_b.jpg',
            'file_name' => 'gear_b.jpg',
            'reference_code' => 'Kamera C',
            'reason' => 'Pergantian katalog',
            'deleted_at' => now(),
        ]);

        // Filter file_type
        $resFilter = $this->actingAs($this->admin)->get(route('admin.files.audit_logs', ['file_type' => 'payment_proof']));
        $resFilter->assertStatus(200);
        $resFilter->assertSee('PAY-SEARCH-TEST');
        $resFilter->assertDontSee('gear_b.jpg');

        // Search
        $resSearch = $this->actingAs($this->admin)->get(route('admin.files.audit_logs', ['search' => 'SEARCH-TEST']));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('PAY-SEARCH-TEST');
        $resSearch->assertDontSee('gear_b.jpg');
    }

    public function test_updating_product_image_cleans_up_old_image(): void
    {
        $oldFile = UploadedFile::fake()->image('old_lens.jpg');
        $oldPath = $oldFile->store('products', 'public');

        $product = $this->createTestProduct([
            'image' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new_lens.jpg');

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'category_id' => $this->category->id,
            'name' => $product->name,
            'price_per_day' => $product->price_per_day,
            'stock' => $product->stock,
            'condition' => $product->condition,
            'image' => $newFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Storage::disk('public')->assertMissing($oldPath);
        $product->refresh();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_store_product_via_form_and_modal(): void
    {
        $file = UploadedFile::fake()->image('sony_fx30.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Sony FX30 Cinema',
            'description' => 'Termasuk 2 baterai dan case.',
            'price_per_day' => 250000,
            'stock' => 3,
            'condition' => 'Mulus 99%',
            'image' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Sony FX30 Cinema',
            'category_id' => $this->category->id,
            'price_per_day' => 250000,
            'stock' => 3,
            'condition' => 'Mulus 99%',
        ]);

        $product = Product::where('name', 'Sony FX30 Cinema')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);

        // Pastikan halaman admin index merender barang, tombol tambah, dan tombol hapus
        $viewRes = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('+ Tambah Barang');
        $viewRes->assertSee('Tambah Barang Baru');
        $viewRes->assertSee('Sony FX30 Cinema');
        $viewRes->assertSee(route('admin.products.destroy', $product->id));
    }

    public function test_admin_cannot_store_product_without_required_fields_but_optional_fields_allowed(): void
    {
        // 1. Gagal jika field wajib tidak diisi
        $failRes = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Barang Tanpa Kategori',
        ]);
        $failRes->assertSessionHasErrors(['category_id', 'price_per_day', 'stock', 'condition']);

        // 2. Berhasil jika hanya field wajib yang diisi (deskripsi dan foto opsional)
        $successRes = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Canon Minimalist',
            'price_per_day' => 150000,
            'stock' => 2,
            'condition' => 'Normal',
        ]);
        $successRes->assertRedirect();
        $successRes->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Canon Minimalist',
            'description' => null,
            'image' => null,
        ]);
    }

    public function test_admin_can_delete_product_without_rental_history(): void
    {
        $file = UploadedFile::fake()->image('to_delete.jpg');
        $path = $file->store('products', 'public');

        $product = $this->createTestProduct([
            'name' => 'Gear Akan Dihapus',
            'image' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Barang berhasil dihapus dari inventaris.');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_cannot_delete_product_with_rental_history(): void
    {
        $product = $this->createTestProduct([
            'name' => 'Kamera Bersejarah Sewa',
        ]);

        $rental = Rental::create([
            'user_id' => $this->customer->id,
            'rental_code' => 'RET-HIST-001',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'total_price' => 300000,
            'status' => 'completed',
        ]);

        RentalItem::create([
            'rental_id' => $rental->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price_per_day' => 150000,
            'subtotal' => 300000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Barang tidak dapat dihapus karena memiliki riwayat transaksi penyewaan.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
}
