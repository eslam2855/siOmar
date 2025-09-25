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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2); // Base price per night
            $table->decimal('weekend_price', 10, 2)->nullable(); // Weekend price
            $table->decimal('holiday_price', 10, 2)->nullable(); // Holiday price
            $table->decimal('weekly_price', 10, 2)->nullable(); // Weekly discount price
            $table->decimal('monthly_price', 10, 2)->nullable(); // Monthly discount price
            $table->decimal('cleaning_fee', 8, 2)->default(0);
            $table->decimal('security_deposit', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
