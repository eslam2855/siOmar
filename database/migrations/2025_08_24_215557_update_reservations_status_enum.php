<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing records to use valid status values
        DB::statement("UPDATE reservations SET status = 'confirmed' WHERE status = 'checked_in'");
        DB::statement("UPDATE reservations SET status = 'completed' WHERE status = 'checked_out'");
        
        // Then modify the enum to include the new status values
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("UPDATE reservations SET status = 'checked_in' WHERE status = 'active'");
        DB::statement("UPDATE reservations SET status = 'checked_out' WHERE status = 'completed'");
        
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending'");
    }
};
