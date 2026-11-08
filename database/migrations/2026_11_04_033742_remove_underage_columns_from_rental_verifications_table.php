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
        Schema::table('rental_verifications', function (Blueprint $table) {
            $columnsToDrop = [
                'customer_type',
                'student_card_file',
                'family_card_file',
                'guardian_name',
                'guardian_phone',
                'guardian_relation',
                'guardian_id_card_number',
                'guardian_id_card_file',
                'guardian_permission_letter_file',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('rental_verifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_verifications', function (Blueprint $table) {
            $table->string('customer_type', 20)->default('adult')->after('user_id');
            $table->string('student_card_file')->nullable()->after('id_card_file');
            $table->string('family_card_file')->nullable()->after('student_card_file');
            $table->string('guardian_name', 150)->nullable()->after('family_card_file');
            $table->string('guardian_phone', 30)->nullable()->after('guardian_name');
            $table->string('guardian_relation', 50)->nullable()->after('guardian_phone');
            $table->string('guardian_id_card_number', 50)->nullable()->after('guardian_relation');
            $table->string('guardian_id_card_file')->nullable()->after('guardian_id_card_number');
            $table->string('guardian_permission_letter_file')->nullable()->after('guardian_id_card_file');
        });
    }
};
