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
        // Add the minimum reservation days setting to the settings table
        \App\Models\Setting::setValue(
            'minimum_reservation_days',
            1,
            'number',
            'reservation',
            'Minimum number of days required for a reservation'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the minimum reservation days setting
        \App\Models\Setting::where('key', 'minimum_reservation_days')->delete();
    }
};
