<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitMonthPrice;
use Illuminate\Database\Seeder;

class UnitMonthPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();

        if ($units->isEmpty()) {
            $this->command->info('No units found. Skipping monthly pricing seeding.');
            return;
        }

        $currentMonth = date('Y-m');
        $nextMonth = date('Y-m', strtotime('+1 month'));
        $nextNextMonth = date('Y-m', strtotime('+2 months'));

        foreach ($units as $unit) {
            // Generate a reasonable daily price for each month
            $basePrice = rand(2000, 5000);
            
            // Create monthly prices for current and next 2 months
            $months = [
                $currentMonth => $basePrice,
                $nextMonth => $basePrice + rand(-500, 1000), // Vary by -500 to +1000
                $nextNextMonth => $basePrice + rand(-300, 800), // Vary by -300 to +800
            ];

            foreach ($months as $yearMonth => $dailyPrice) {
                UnitMonthPrice::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'year_month' => $yearMonth,
                    ],
                    [
                        'daily_price' => $basePrice + rand(-500, 1000), // Vary the price
                        'currency' => 'EGP',
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Monthly pricing seeded successfully!');
        $this->command->info("Created monthly pricing for {$units->count()} units across 3 months.");
    }
}
