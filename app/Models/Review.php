<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_id',
        'reservation_id',
        'overall_rating',
        'room_rating',
        'service_rating',
        'pricing_rating',
        'location_rating',
        'review_text',
        'is_approved',
        'reviewed_at',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'room_rating' => 'integer',
        'service_rating' => 'integer',
        'pricing_rating' => 'integer',
        'location_rating' => 'integer',
        'is_approved' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Calculate the overall rating from individual category ratings
     */
    public function calculateOverallRating(): float
    {
        $ratings = array_filter([
            $this->room_rating,
            $this->service_rating,
            $this->pricing_rating,
            $this->location_rating
        ]);

        return !empty($ratings) ? round(array_sum($ratings) / count($ratings), 1) : 0;
    }

    /**
     * Boot method to automatically calculate overall rating
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($review) {
            if ($review->room_rating || $review->service_rating || $review->pricing_rating || $review->location_rating) {
                $review->overall_rating = $review->calculateOverallRating();
            }
        });
    }
}
