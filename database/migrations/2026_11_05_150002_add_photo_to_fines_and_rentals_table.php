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
        Schema::table('fines', function (Blueprint $table) {
            if (! Schema::hasColumn('fines', 'photo')) {
                $table->string('photo', 255)->nullable()->after('reason');
            }
        });

        Schema::table('rentals', function (Blueprint $table) {
            if (! Schema::hasColumn('rentals', 'return_photo')) {
                $table->string('return_photo', 255)->nullable()->after('returned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            if (Schema::hasColumn('fines', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'return_photo')) {
                $table->dropColumn('return_photo');
            }
        });
    }
};
