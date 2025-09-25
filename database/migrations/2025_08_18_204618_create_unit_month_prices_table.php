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
        Schema::create('unit_month_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('year_month', 7)->comment('YYYY-MM format for the month');
            $table->decimal('daily_price', 10, 2)->comment('Daily rate for this month');
            $table->string('currency', 3)->default('EGP')->comment('Currency code');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique pricing per unit per month
            $table->unique(['unit_id', 'year_month']);
            
            // Index for efficient queries
            $table->index(['unit_id', 'year_month', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_month_prices');
    }
};
