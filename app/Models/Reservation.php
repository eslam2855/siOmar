<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_id',
        'cancellation_policy_id',
        'reservation_number',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'total_amount',
        'cleaning_fee',
        'security_deposit',
        'status',
        'payment_status',
        'guest_name',
        'guest_phone',
        'guest_email',
        'special_requests',
        'early_check_in_time',
        'late_check_out_time',
        'early_check_in_requested',
        'late_check_out_requested',
        'cancellation_reason',
        'refund_amount',
        'admin_notes',
        'transfer_image',
        'transfer_amount',
        'transfer_date',
        'deposit_amount',
        'deposit_image',
        'minimum_deposit_amount',
        'deposit_percentage',
        'deposit_verified',
        'deposit_verified_at',
        'confirmed_at',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'refunded_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'number_of_guests' => 'integer',
        'total_amount' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'transfer_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'minimum_deposit_amount' => 'decimal:2',
        'deposit_percentage' => 'decimal:2',
        'early_check_in_requested' => 'boolean',
        'late_check_out_requested' => 'boolean',
        'deposit_verified' => 'boolean',
        'early_check_in_time' => 'datetime',
        'late_check_out_time' => 'datetime',
        'transfer_date' => 'datetime',
        'deposit_verified_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_PARTIALLY_REFUNDED = 'partially_refunded';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Check if reservation can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }

        if ($this->status === self::STATUS_COMPLETED) {
            return false;
        }

        // Check if within cancellation window
        if ($this->cancellationPolicy) {
            return $this->cancellationPolicy->canCancelReservation($this);
        }

        return true; // Default to allowing cancellation if no policy set
    }

    /**
     * Cancel the reservation
     */
    public function cancel(string $reason = null, float $refundAmount = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;

        // Calculate refund amount if not provided
        if ($refundAmount === null && $this->cancellationPolicy) {
            $refundAmount = $this->cancellationPolicy->calculateRefundAmount($this);
        }

        $this->refund_amount = $refundAmount;

        // Update payment status
        if ($refundAmount > 0) {
            $this->payment_status = $refundAmount >= $this->total_amount ? 
                self::PAYMENT_REFUNDED : self::PAYMENT_PARTIALLY_REFUNDED;
            $this->refunded_at = now();
        }

        return $this->save();
    }

    /**
     * Confirm the reservation
     */
    public function confirm(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->confirmed_at = now();
        
        return $this->save();
    }

    /**
     * Activate the reservation (guest checked in)
     */
    public function activate(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            return false;
        }

        $this->status = self::STATUS_ACTIVE;
        $this->activated_at = now();
        
        return $this->save();
    }

    /**
     * Complete the reservation (guest checked out)
     */
    public function complete(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        
        return $this->save();
    }

    /**
     * Get the number of nights for this reservation
     */
    public function getNightsCount(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Check if reservation is currently active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if reservation is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if reservation is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if reservation is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_ACTIVE => 'green',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CANCELLED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeColor(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'yellow',
            self::PAYMENT_PAID => 'green',
            self::PAYMENT_FAILED => 'red',
            self::PAYMENT_REFUNDED => 'blue',
            self::PAYMENT_PARTIALLY_REFUNDED => 'orange',
            default => 'gray'
        };
    }

    /**
     * Check if deposit is sufficient
     */
    public function isDepositSufficient(): bool
    {
        if (!$this->minimum_deposit_amount) {
            return true; // No minimum deposit required
        }

        return $this->transfer_amount >= $this->minimum_deposit_amount;
    }

    /**
     * Get deposit status
     */
    public function getDepositStatus(): string
    {
        if (!$this->transfer_amount) {
            return 'pending';
        }

        if (!$this->deposit_verified) {
            return 'pending_verification';
        }

        if ($this->isDepositSufficient()) {
            return 'sufficient';
        }

        return 'insufficient';
    }

    /**
     * Verify deposit
     */
    public function verifyDeposit(): bool
    {
        if (!$this->transfer_amount) {
            return false;
        }

        $this->deposit_verified = true;
        $this->deposit_verified_at = now();
        
        return $this->save();
    }

    /**
     * Get transfer image URL
     */
    public function getTransferImageUrl(): ?string
    {
        if (!$this->transfer_image) {
            return null;
        }

        return asset('storage/' . $this->transfer_image);
    }

    /**
     * Calculate deposit amount based on percentage
     */
    public function calculateDepositAmount(): ?float
    {
        if (!$this->deposit_percentage || !$this->total_amount) {
            return null;
        }

        return ($this->total_amount * $this->deposit_percentage) / 100;
    }

    /**
     * Get deposit amount (either fixed amount or calculated from percentage)
     */
    public function getDepositAmount(): ?float
    {
        // If minimum_deposit_amount is set, use it
        if ($this->minimum_deposit_amount) {
            return $this->minimum_deposit_amount;
        }

        // Otherwise calculate from percentage
        return $this->calculateDepositAmount();
    }

    /**
     * Check if reservation can be confirmed
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending' && $this->deposit_verified;
    }

    /**
     * Check if reservation can be activated
     */
    public function canBeActivated(): bool
    {
        return $this->status === 'confirmed' && now()->gte($this->check_in_date);
    }

    /**
     * Check if reservation can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'active' && now()->gte($this->check_out_date);
    }

    /**
     * Calculate number of nights
     */
    public function getNumberOfNights(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Check if reservation is overdue for activation
     */
    public function isOverdueForActivation(): bool
    {
        return $this->status === 'confirmed' && now()->gt($this->check_in_date);
    }

    /**
     * Check if reservation is overdue for completion
     */
    public function isOverdueForCompletion(): bool
    {
        return $this->status === 'active' && now()->gt($this->check_out_date);
    }

    /**
     * Scope to get upcoming reservations (check-in date is in the future)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now()->startOfDay())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Scope to get current/active reservations (currently ongoing)
     */
    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_ACTIVE)
                ->orWhere(function ($subQ) {
                    $subQ->where('check_in_date', '<=', now()->endOfDay())
                        ->where('check_out_date', '>=', now()->startOfDay())
                        ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_ACTIVE]);
                });
            });
    }

    /**
     * Scope to get finished/completed reservations (check-out date is in the past)
     */
    public function scopeFinished($query)
    {
        return $query->where(function ($q) {
            $q->where('check_out_date', '<', now()->startOfDay())
                ->orWhereIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
        });
    }

    /**
     * Scope to get reservations by filter type
     */
    public function scopeByFilter($query, $filter)
    {
        return match($filter) {
            'upcoming' => $query->upcoming(),
            'current' => $query->current(),
            'finished' => $query->finished(),
            default => $query
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = 'RES-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }
}
