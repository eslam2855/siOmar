<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'profile_image' => $this->user->profile_image, // Keep original path for backward compatibility
                'profile_image_url' => $this->user->profile_image_url, // Full URL with storage path for mobile apps
            ],
            'overall_rating' => $this->overall_rating,
            'room_rating' => $this->room_rating,
            'service_rating' => $this->service_rating,
            'pricing_rating' => $this->pricing_rating,
            'location_rating' => $this->location_rating,
            'review_text' => $this->review_text,
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
