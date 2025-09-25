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
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard", "Flexible", "Strict"
            $table->text('description');
            $table->integer('cancellation_hours'); // Hours before check-in when cancellation is allowed
            $table->decimal('refund_percentage', 5, 2); // Percentage of refund (0-100)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default policy for new units
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
