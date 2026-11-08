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
        Schema::create('admin_file_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_type', 100); // payment_proof, id_card_file, product_image, promotion_image, fine_photo, qris_image
            $table->string('file_path', 255);
            $table->string('file_name', 255)->nullable();
            $table->string('related_model_type', 150)->nullable();
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->string('reference_code', 100)->nullable(); // e.g., rental code, invoice, product name
            $table->text('reason')->nullable();
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();

            $table->index(['file_type', 'created_at']);
            $table->index(['related_model_type', 'related_model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_file_audit_logs');
    }
};
