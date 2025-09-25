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
            // Comment out current pricing columns (add comment to indicate they are deprecated)
            $table->comment('base_price')->change(); // Comment: Deprecated - Use monthly pricing instead
            $table->comment('weekend_price')->change(); // Comment: Deprecated - Use monthly pricing instead
            $table->comment('holiday_price')->change(); // Comment: Deprecated - Use monthly pricing instead
            $table->comment('weekly_price')->change(); // Comment: Deprecated - Use monthly pricing instead
            $table->comment('monthly_price')->change(); // Comment: Deprecated - Use monthly pricing instead
            
            // Add new monthly pricing fields
            $table->decimal('current_month_price', 10, 2)->nullable()->comment('Price for current month');
            $table->decimal('next_month_price', 10, 2)->nullable()->comment('Price for next month');
            $table->decimal('next_next_month_price', 10, 2)->nullable()->comment('Price for month after next');
            
            // Add month fields to track which months these prices apply to
            $table->string('current_month', 7)->nullable()->comment('YYYY-MM format for current month');
            $table->string('next_month', 7)->nullable()->comment('YYYY-MM format for next month');
            $table->string('next_next_month', 7)->nullable()->comment('YYYY-MM format for month after next');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing', function (Blueprint $table) {
            // Remove new monthly pricing fields
            $table->dropColumn([
                'current_month_price',
                'next_month_price', 
                'next_next_month_price',
                'current_month',
                'next_month',
                'next_next_month'
            ]);
            
            // Remove comments from old columns
            $table->comment('base_price')->change(); // Remove comment
            $table->comment('weekend_price')->change(); // Remove comment
            $table->comment('holiday_price')->change(); // Remove comment
            $table->comment('weekly_price')->change(); // Remove comment
            $table->comment('monthly_price')->change(); // Remove comment
        });
    }
};
