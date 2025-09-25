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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('transfer_date');
            }
            if (!Schema::hasColumn('reservations', 'deposit_image')) {
                $table->string('deposit_image')->nullable()->after('deposit_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'deposit_amount')) {
                $table->dropColumn('deposit_amount');
            }
            if (Schema::hasColumn('reservations', 'deposit_image')) {
                $table->dropColumn('deposit_image');
            }
        });
    }
};
