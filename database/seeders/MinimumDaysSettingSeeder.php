<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MinimumDaysSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::setValue(
            'minimum_reservation_days',
            1,
            'number',
            'reservation',
            'Minimum number of days required for a reservation'
        );
    }
}
