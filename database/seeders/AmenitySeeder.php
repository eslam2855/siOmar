<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\SeederHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    use SeederHelper;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'WiFi',
                'icon' => 'wifi',
                'description' => 'Free high-speed internet access',
                'is_active' => true,
            ],
            [
                'name' => 'Air Conditioning',
                'icon' => 'ac',
                'description' => 'Air conditioning for comfort',
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen',
                'icon' => 'kitchen',
                'description' => 'Fully equipped kitchen',
                'is_active' => true,
            ],
            [
                'name' => 'Parking',
                'icon' => 'parking',
                'description' => 'Free parking available',
                'is_active' => true,
            ],
            [
                'name' => 'Balcony',
                'icon' => 'balcony',
                'description' => 'Private balcony with view',
                'is_active' => true,
            ],
            [
                'name' => 'TV',
                'icon' => 'tv',
                'description' => 'Flat-screen TV with cable',
                'is_active' => true,
            ],
            [
                'name' => 'Washing Machine',
                'icon' => 'washing-machine',
                'description' => 'In-unit washing machine',
                'is_active' => true,
            ],
            [
                'name' => 'Gym',
                'icon' => 'gym',
                'description' => 'Access to fitness center',
                'is_active' => true,
            ],
            [
                'name' => 'Pool',
                'icon' => 'pool',
                'description' => 'Swimming pool access',
                'is_active' => true,
            ],
            [
                'name' => 'Security',
                'icon' => 'security',
                'description' => '24/7 security service',
                'is_active' => true,
            ],
        ];

        $this->createMultipleWithoutDuplicates(Amenity::class, $amenities, 'name');
    }
}
