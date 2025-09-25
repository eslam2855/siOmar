<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Welcome to SiOmar',
                'image' => 'sliders/welcome-slider.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Luxury Accommodations',
                'image' => 'sliders/luxury-slider.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Perfect Getaway',
                'image' => 'sliders/getaway-slider.jpg',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Book Your Stay',
                'image' => 'sliders/book-slider.jpg',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
