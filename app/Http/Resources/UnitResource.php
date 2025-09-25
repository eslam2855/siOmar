<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'unit_number' => $this->unit_number,
            'description' => $this->description,
            'status' => $this->status,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'max_guests' => $this->max_guests,
            'size' => $this->size,
            'address' => $this->address,
            'unit_type' => $this->whenLoaded('unitType'),
            'images' => $this->whenLoaded('images', function () {
                return \App\Http\Resources\UnitImageResource::collection($this->images);
            }),
            'primary_image' => $this->whenLoaded('primaryImage', function () {
                return $this->primaryImage ? new \App\Http\Resources\UnitImageResource($this->primaryImage) : null;
            }),
            // For reservation API, show primary image as the main image
            'image' => $this->whenLoaded('primaryImage', function () {
                return $this->primaryImage ? new \App\Http\Resources\UnitImageResource($this->primaryImage) : null;
            }),
            'amenities' => $this->whenLoaded('amenities'),
            'monthly_pricing' => $this->getMonthlyPrices(3),
            'minimum_reservation_days' => \App\Models\Setting::getValue('minimum_reservation_days', 1),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];

        // Add rating statistics
        $data['rating_statistics'] = [
            'averages' => [
                'overall' => round($this->getAverageOverallRating(), 1),
                'room' => round($this->getAverageRatingForCategory('room'), 1),
                'service' => round($this->getAverageRatingForCategory('service'), 1),
                'pricing' => round($this->getAverageRatingForCategory('pricing'), 1),
                'location' => round($this->getAverageRatingForCategory('location'), 1),
            ],
            'total_reviews' => $this->getTotalReviewsCount(),
        ];

        // Add reviews list only for unit details (when reviews are loaded)
        if ($this->relationLoaded('reviews')) {
            $data['reviews'] = ReviewResource::collection($this->approvedReviews);
        }

        return $data;
    }
}
