<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();
        $users = User::all();

        if ($units->isEmpty() || $users->isEmpty()) {
            $this->command->info('No units or users found. Skipping review seeding.');
            return;
        }

        $reviews = [
            [
                'room_rating' => 5,
                'service_rating' => 4,
                'pricing_rating' => 4,
                'location_rating' => 5,
                'review_text' => 'Excellent room with amazing views! The service was great and the location is perfect.',
            ],
            [
                'room_rating' => 4,
                'service_rating' => 5,
                'pricing_rating' => 3,
                'location_rating' => 4,
                'review_text' => 'Very comfortable room and outstanding service. Price is a bit high but worth it.',
            ],
            [
                'room_rating' => 5,
                'service_rating' => 5,
                'pricing_rating' => 5,
                'location_rating' => 4,
                'review_text' => 'Perfect stay! Everything was exceptional, especially the room quality and service.',
            ],
            [
                'room_rating' => 3,
                'service_rating' => 4,
                'pricing_rating' => 5,
                'location_rating' => 3,
                'review_text' => 'Good value for money. Room was decent, service was good, but location could be better.',
            ],
            [
                'room_rating' => 4,
                'service_rating' => 3,
                'pricing_rating' => 4,
                'location_rating' => 5,
                'review_text' => 'Great location and comfortable room. Service was okay, pricing is reasonable.',
            ],
        ];

        foreach ($units as $unit) {
            // Add 2-4 reviews per unit
            $numReviews = rand(2, 4);
            
            for ($i = 0; $i < $numReviews; $i++) {
                $reviewData = $reviews[array_rand($reviews)];
                
                Review::create([
                    'user_id' => $users->random()->id,
                    'unit_id' => $unit->id,
                    'reservation_id' => null, // We'll leave this null for now
                    'room_rating' => $reviewData['room_rating'],
                    'service_rating' => $reviewData['service_rating'],
                    'pricing_rating' => $reviewData['pricing_rating'],
                    'location_rating' => $reviewData['location_rating'],
                    'review_text' => $reviewData['review_text'],
                    'is_approved' => true,
                    'reviewed_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Reviews seeded successfully!');
    }
}
