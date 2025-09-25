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
        Schema::table('reviews', function (Blueprint $table) {
            // Rename existing rating to overall_rating
            $table->renameColumn('rating', 'overall_rating');
            
            // Add new rating categories
            $table->integer('room_rating')->nullable()->comment('1-5 stars for room quality');
            $table->integer('service_rating')->nullable()->comment('1-5 stars for service quality');
            $table->integer('pricing_rating')->nullable()->comment('1-5 stars for pricing value');
            $table->integer('location_rating')->nullable()->comment('1-5 stars for location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Remove new rating categories
            $table->dropColumn(['room_rating', 'service_rating', 'pricing_rating', 'location_rating']);
            
            // Rename back to original
            $table->renameColumn('overall_rating', 'rating');
        });
    }
};
