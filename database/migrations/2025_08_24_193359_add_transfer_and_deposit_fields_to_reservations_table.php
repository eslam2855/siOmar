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
            // Add missing deposit-related fields only
            if (!Schema::hasColumn('reservations', 'minimum_deposit_amount')) {
                $table->decimal('minimum_deposit_amount', 10, 2)->nullable()->comment('Minimum deposit required to approve reservation');
            }
            
            if (!Schema::hasColumn('reservations', 'deposit_verified')) {
                $table->boolean('deposit_verified')->default(false)->comment('Whether the deposit has been verified by admin');
            }
            
            if (!Schema::hasColumn('reservations', 'deposit_verified_at')) {
                $table->timestamp('deposit_verified_at')->nullable()->comment('When the deposit was verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $columns = [
                'minimum_deposit_amount',
                'deposit_verified',
                'deposit_verified_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('reservations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
