<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->cascadeOnDelete()->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Klasifikasi Usia & Identitas
            $table->string('customer_type', 20)->default('adult'); // 'adult' (dewasa >=17th) atau 'underage' (<17th)
            $table->string('id_card_number', 50)->nullable();
            $table->string('id_card_file')->nullable(); // Disimpan di storage private

            // Dokumen Alternatif Khusus Pelanggan Di Bawah Umur (<17 tahun)
            $table->string('student_card_file')->nullable();
            $table->string('family_card_file')->nullable();
            $table->string('guardian_name', 150)->nullable();
            $table->string('guardian_phone', 30)->nullable();
            $table->string('guardian_relation', 50)->nullable(); // 'ayah', 'ibu', 'wali'
            $table->string('guardian_id_card_number', 50)->nullable();
            $table->string('guardian_id_card_file')->nullable();
            $table->string('guardian_permission_letter_file')->nullable();

            // Formulir Penyewaan Tambahan
            $table->string('rental_purpose', 150)->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();

            // Persetujuan, Bea Meterai, dan Tanda Tangan Digital
            $table->longText('signature_data')->nullable(); // Base64 data URL tanda tangan
            $table->dateTime('agreement_signed_at')->nullable();
            $table->string('agreement_ip_address', 45)->nullable();
            $table->boolean('requires_stamp_duty')->default(false); // Transaksi >= Rp 5.000.000 (UU Bea Meterai No. 10/2020)
            $table->decimal('stamp_duty_amount', 12, 2)->default(0);

            // Status Verifikasi Dokumen oleh Admin
            $table->string('verification_status', 30)->default('menunggu'); // 'menunggu', 'disetujui', 'ditolak', 'perlu_revisi'
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index(['rental_id', 'verification_status']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_verifications');
    }
};
