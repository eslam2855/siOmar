<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancellationPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cancellation_hours',
        'refund_percentage',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'cancellation_hours' => 'integer',
        'refund_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    /**
     * Get all units using this cancellation policy
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all reservations using this cancellation policy
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if a reservation can be cancelled based on this policy
     */
    public function canCancelReservation(Reservation $reservation): bool
    {
        $checkInTime = $reservation->check_in;
        $currentTime = now();
        $hoursUntilCheckIn = $checkInTime->diffInHours($currentTime);
        
        return $hoursUntilCheckIn >= $this->cancellation_hours;
    }

    /**
     * Calculate refund amount for a cancelled reservation
     */
    public function calculateRefundAmount(Reservation $reservation): float
    {
        if ($this->canCancelReservation($reservation)) {
            return ($reservation->total_amount * $this->refund_percentage) / 100;
        }
        
        return 0;
    }

    /**
     * Get formatted cancellation window
     */
    public function getFormattedCancellationWindow(): string
    {
        if ($this->cancellation_hours >= 24) {
            $days = floor($this->cancellation_hours / 24);
            $hours = $this->cancellation_hours % 24;
            
            if ($hours > 0) {
                return "{$days} days, {$hours} hours";
            }
            return "{$days} days";
        }
        
        return "{$this->cancellation_hours} hours";
    }

    /**
     * Scope to get only active policies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default policy
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
