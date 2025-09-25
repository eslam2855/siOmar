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
        Schema::table('pricing', function (Blueprint $table) {
            // Remove legacy pricing fields
            $table->dropColumn([
                'base_price',
                'weekend_price', 
                'holiday_price',
                'weekly_price',
                'monthly_price',
                'current_month_price',
                'next_month_price',
                'next_next_month_price',
                'current_month',
                'next_month',
                'next_next_month'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing', function (Blueprint $table) {
            // Re-add legacy pricing fields
            $table->decimal('base_price', 10, 2)->comment('Base price per night');
            $table->decimal('weekend_price', 10, 2)->nullable()->comment('Weekend price');
            $table->decimal('holiday_price', 10, 2)->nullable()->comment('Holiday price');
            $table->decimal('weekly_price', 10, 2)->nullable()->comment('Weekly discount price');
            $table->decimal('monthly_price', 10, 2)->nullable()->comment('Monthly discount price');
            
            // Re-add monthly pricing fields
            $table->decimal('current_month_price', 10, 2)->nullable()->comment('Price for current month');
            $table->decimal('next_month_price', 10, 2)->nullable()->comment('Price for next month');
            $table->decimal('next_next_month_price', 10, 2)->nullable()->comment('Price for month after next');
            $table->string('current_month', 7)->nullable()->comment('YYYY-MM format for current month');
            $table->string('next_month', 7)->nullable()->comment('YYYY-MM format for next month');
            $table->string('next_next_month', 7)->nullable()->comment('YYYY-MM format for month after next');
        });
    }
};
