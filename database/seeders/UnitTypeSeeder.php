<?php

namespace Database\Seeders;

use App\Models\UnitType;
use App\SeederHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypes = [
            [
                'name' => 'Panglo',
                'description' => 'Cozy panglo units perfect for solo travelers or couples',
                'max_capacity' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Studio',
                'description' => 'Modern studio apartments with all amenities included',
                'max_capacity' => 2,
                'is_active' => true,
            ]
        ];

        $this->createMultipleWithoutDuplicates(UnitType::class, $unitTypes, 'name');
    }
}
