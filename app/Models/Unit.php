<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_type_id',
        'name',
        'unit_number',
        'description',
        'size_sqm',
        'bedrooms',
        'bathrooms',
        'max_guests',
        'status',
        'latitude',
        'longitude',
        'address',
        'is_active',
    ];

    protected $casts = [
        'size_sqm' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'max_guests' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(UnitType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'unit_amenities');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(Pricing::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UnitImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(UnitImage::class)->where('is_primary', true);
    }

    public function monthPrices(): HasMany
    {
        return $this->hasMany(UnitMonthPrice::class);
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    /**
     * Get approved reviews for this unit
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Get the daily price for a specific date
     */
    public function getDailyPriceForDate(Carbon $date): ?float
    {
        $yearMonth = $date->format('Y-m');
        
        $monthPrice = $this->monthPrices()
            ->active()
            ->forMonth($yearMonth)
            ->first();
            
        if ($monthPrice) {
            return $monthPrice->daily_price;
        }
        
        // No fallback price available
        return null;
    }

    /**
     * Get monthly prices for the next N months
     */
    public function getMonthlyPrices(int $months = 3): array
    {
        $prices = [];
        $currentDate = Carbon::now();
        
        for ($i = 0; $i < $months; $i++) {
            $monthDate = $currentDate->copy()->addMonths($i);
            $yearMonth = $monthDate->format('Y-m');
            
            $monthPrice = $this->monthPrices()
                ->active()
                ->forMonth($yearMonth)
                ->first();
                
            $prices[] = [
                'month' => $yearMonth,
                'formatted_month' => $monthDate->format('F Y'),
                'daily_price' => $monthPrice ? $monthPrice->daily_price : null,
                'currency' => $monthPrice ? $monthPrice->currency : 'EGP',
                'is_active' => $monthPrice ? $monthPrice->is_active : false,
            ];
        }
        
        return $prices;
    }

    /**
     * Calculate total price for a date range
     */
    public function calculateTotalPriceForRange(Carbon $startDate, Carbon $endDate): array
    {
        $totalPrice = 0;
        $breakdown = [];
        $currentDate = $startDate->copy();
        $hasValidPricing = false;
        
        while ($currentDate->lt($endDate)) {
            $yearMonth = $currentDate->format('Y-m');
            $dailyPrice = $this->getDailyPriceForDate($currentDate);
            
            if ($dailyPrice) {
                $hasValidPricing = true;
                $totalPrice += $dailyPrice;
                
                if (!isset($breakdown[$yearMonth])) {
                    $breakdown[$yearMonth] = [
                        'month' => $yearMonth,
                        'formatted_month' => $currentDate->format('F Y'),
                        'daily_price' => $dailyPrice,
                        'nights' => 0,
                        'subtotal' => 0,
                    ];
                }
                
                $breakdown[$yearMonth]['nights']++;
                $breakdown[$yearMonth]['subtotal'] += $dailyPrice;
            }
            
            $currentDate->addDay();
        }
        
        // Add fees
        $cleaningFee = $this->pricing ? $this->pricing->cleaning_fee : 0;
        $securityDeposit = $this->pricing ? $this->pricing->security_deposit : 0;
        
        return [
            'total_price' => $totalPrice,
            'cleaning_fee' => $cleaningFee,
            'security_deposit' => $securityDeposit,
            'grand_total' => $totalPrice + $cleaningFee + $securityDeposit,
            'breakdown' => array_values($breakdown),
            'nights' => $startDate->diffInDays($endDate),
            'has_valid_pricing' => $hasValidPricing,
        ];
    }

    /**
     * Calculate average overall rating
     */
    public function getAverageOverallRating(): float
    {
        return $this->approvedReviews()->avg('overall_rating') ?? 0;
    }

    /**
     * Calculate average rating for a specific category
     */
    public function getAverageRatingForCategory(string $category): float
    {
        $column = $category . '_rating';
        return $this->approvedReviews()->avg($column) ?? 0;
    }

    /**
     * Get all average ratings
     */
    public function getAllAverageRatings(): array
    {
        return [
            'overall' => round($this->getAverageOverallRating(), 1),
            'room' => round($this->getAverageRatingForCategory('room'), 1),
            'service' => round($this->getAverageRatingForCategory('service'), 1),
            'pricing' => round($this->getAverageRatingForCategory('pricing'), 1),
            'location' => round($this->getAverageRatingForCategory('location'), 1),
        ];
    }

    /**
     * Get total number of reviews
     */
    public function getTotalReviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get rating statistics
     */
    public function getRatingStatistics(): array
    {
        return [
            'averages' => $this->getAllAverageRatings(),
            'total_reviews' => $this->getTotalReviewsCount(),
        ];
    }
}
