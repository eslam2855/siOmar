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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // notification type (reservation, payment, system, etc.)
            $table->string('title'); // notification title
            $table->text('message'); // notification message
            $table->json('data')->nullable(); // additional data (reservation_id, amount, etc.)
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('category')->default('general'); // reservation, payment, system, legal, etc.
            $table->boolean('is_global')->default(false); // global notification for all users
            $table->json('target_roles')->nullable(); // specific roles to target
            $table->json('target_users')->nullable(); // specific user IDs to target
            $table->timestamp('scheduled_at')->nullable(); // schedule notification for later
            $table->timestamp('expires_at')->nullable(); // notification expiration
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['scheduled_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
