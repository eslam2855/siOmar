<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Unit;
use App\Models\Slider;
use App\Models\User;

class CacheService
{
    /**
     * Cache keys constants
     */
    const CACHE_KEYS = [
        'ACTIVE_UNITS' => 'active_units',
        'SLIDERS' => 'sliders',
        'UNIT_TYPES' => 'unit_types',
        'AMENITIES' => 'amenities',
        'USER_STATS' => 'user_stats',
        'DASHBOARD_STATS' => 'dashboard_stats',
    ];

    /**
     * Cache duration constants (in seconds)
     */
    const CACHE_DURATIONS = [
        'SHORT' => 300,    // 5 minutes
        'MEDIUM' => 1800,  // 30 minutes
        'LONG' => 3600,    // 1 hour
        'VERY_LONG' => 86400, // 24 hours
    ];

    /**
     * Get active units with caching.
     */
    public function getActiveUnits()
    {
        return Cache::remember(
            self::CACHE_KEYS['ACTIVE_UNITS'],
            self::CACHE_DURATIONS['MEDIUM'],
            function () {
                return Unit::with(['images', 'amenities', 'pricing', 'unitType'])
                    ->where('status', 'available')
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        );
    }

    /**
     * Get sliders with caching.
     */
    public function getSliders()
    {
        return Cache::remember(
            self::CACHE_KEYS['SLIDERS'],
            self::CACHE_DURATIONS['LONG'],
            function () {
                return Slider::where('is_active', true)
                    ->orderBy('display_order')
                    ->get();
            }
        );
    }

    /**
     * Get unit types with caching.
     */
    public function getUnitTypes()
    {
        return Cache::remember(
            self::CACHE_KEYS['UNIT_TYPES'],
            self::CACHE_DURATIONS['VERY_LONG'],
            function () {
                return \App\Models\UnitType::all();
            }
        );
    }

    /**
     * Get amenities with caching.
     */
    public function getAmenities()
    {
        return Cache::remember(
            self::CACHE_KEYS['AMENITIES'],
            self::CACHE_DURATIONS['VERY_LONG'],
            function () {
                return \App\Models\Amenity::all();
            }
        );
    }

    /**
     * Get dashboard statistics with caching.
     */
    public function getDashboardStats()
    {
        return Cache::remember(
            self::CACHE_KEYS['DASHBOARD_STATS'],
            self::CACHE_DURATIONS['SHORT'],
            function () {
                return [
                    'total_users' => User::count(),
                    'total_units' => Unit::count(),
                    'available_units' => Unit::where('status', 'available')->where('is_active', true)->count(),
                    'total_reservations' => \App\Models\Reservation::count(),
                    'pending_reservations' => \App\Models\Reservation::where('status', 'pending')->count(),
                    'total_revenue' => \App\Models\Reservation::where('status', 'confirmed')->sum('total_amount'),
                ];
            }
        );
    }

    /**
     * Clear specific cache.
     */
    public function clearCache(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear all application cache.
     */
    public function clearAllCache(): bool
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
        
        return true;
    }

    /**
     * Clear cache when models are updated.
     */
    public function clearModelCache(string $model): void
    {
        switch ($model) {
            case 'Unit':
                $this->clearCache(self::CACHE_KEYS['ACTIVE_UNITS']);
                $this->clearCache(self::CACHE_KEYS['DASHBOARD_STATS']);
                break;
            case 'Slider':
                $this->clearCache(self::CACHE_KEYS['SLIDERS']);
                break;
            case 'User':
                $this->clearCache(self::CACHE_KEYS['DASHBOARD_STATS']);
                break;
            case 'Reservation':
                $this->clearCache(self::CACHE_KEYS['DASHBOARD_STATS']);
                break;
        }
    }

    /**
     * Get cache statistics.
     */
    public function getCacheStats(): array
    {
        return [
            'active_units_cache' => Cache::has(self::CACHE_KEYS['ACTIVE_UNITS']),
            'sliders_cache' => Cache::has(self::CACHE_KEYS['SLIDERS']),
            'unit_types_cache' => Cache::has(self::CACHE_KEYS['UNIT_TYPES']),
            'amenities_cache' => Cache::has(self::CACHE_KEYS['AMENITIES']),
            'dashboard_stats_cache' => Cache::has(self::CACHE_KEYS['DASHBOARD_STATS']),
        ];
    }
}
