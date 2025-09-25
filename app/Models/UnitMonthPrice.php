<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UnitMonthPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'year_month',
        'daily_price',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the formatted month name
     */
    public function getFormattedMonthAttribute(): string
    {
        return Carbon::createFromFormat('Y-m', $this->year_month)->format('F Y');
    }

    /**
     * Get the month as Carbon instance
     */
    public function getMonthAttribute(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->year_month);
    }

    /**
     * Scope to get active prices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get prices for a specific month
     */
    public function scopeForMonth($query, string $yearMonth)
    {
        return $query->where('year_month', $yearMonth);
    }

    /**
     * Scope to get prices for a date range
     */
    public function scopeForDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        $startMonth = $startDate->format('Y-m');
        $endMonth = $endDate->format('Y-m');
        
        return $query->whereBetween('year_month', [$startMonth, $endMonth]);
    }
}
