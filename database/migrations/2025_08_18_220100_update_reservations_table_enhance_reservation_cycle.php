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
            // Add missing columns only
            if (!Schema::hasColumn('reservations', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            }
            
            if (!Schema::hasColumn('reservations', 'guest_name')) {
                $table->string('guest_name')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'guest_phone')) {
                $table->string('guest_phone')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'guest_email')) {
                $table->string('guest_email')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'early_check_in_time')) {
                $table->time('early_check_in_time')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'late_check_out_time')) {
                $table->time('late_check_out_time')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'early_check_in_requested')) {
                $table->boolean('early_check_in_requested')->default(false);
            }
            
            if (!Schema::hasColumn('reservations', 'late_check_out_requested')) {
                $table->boolean('late_check_out_requested')->default(false);
            }
            
            if (!Schema::hasColumn('reservations', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'activated_at')) {
                $table->timestamp('activated_at')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
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
                'payment_status',
                'guest_name',
                'guest_phone',
                'guest_email',
                'early_check_in_time',
                'late_check_out_time',
                'early_check_in_requested',
                'late_check_out_requested',
                'refund_amount',
                'refunded_at',
                'admin_notes',
                'activated_at',
                'completed_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('reservations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
