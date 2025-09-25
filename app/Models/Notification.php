<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'priority',
        'category',
        'is_global',
        'target_roles',
        'target_users',
        'scheduled_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'data' => 'array',
        'target_roles' => 'array',
        'target_users' => 'array',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Notification types
    const TYPE_RESERVATION = 'reservation';
    const TYPE_PAYMENT = 'payment';
    const TYPE_SYSTEM = 'system';
    const TYPE_LEGAL = 'legal';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_SECURITY = 'security';

    // Priorities
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Categories
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_RESERVATION = 'reservation';
    const CATEGORY_PAYMENT = 'payment';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_LEGAL = 'legal';
    const CATEGORY_MAINTENANCE = 'maintenance';

    /**
     * Get all user notifications for this notification
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Get users who received this notification
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notifications')
            ->withPivot(['is_read', 'read_at', 'is_sent', 'sent_at', 'is_delivered', 'delivered_at', 'delivery_data'])
            ->withTimestamps();
    }

    /**
     * Scope for active notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for scheduled notifications
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope for non-expired notifications
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for global notifications
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope for role-specific notifications
     */
    public function scopeForRole($query, $role)
    {
        return $query->whereJsonContains('target_roles', $role);
    }

    /**
     * Scope for user-specific notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereJsonContains('target_users', $userId);
    }

    /**
     * Check if notification is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if notification is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    /**
     * Check if notification should be sent now
     */
    public function shouldBeSent(): bool
    {
        return $this->is_active 
            && !$this->isExpired() 
            && (!$this->scheduled_at || $this->scheduled_at->isPast());
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'blue',
        };
    }

    /**
     * Get priority icon for UI
     */
    public function getPriorityIconAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'info',
            self::PRIORITY_NORMAL => 'bell',
            self::PRIORITY_HIGH => 'exclamation-triangle',
            self::PRIORITY_URGENT => 'exclamation-circle',
            default => 'bell',
        };
    }

    /**
     * Create a new notification
     */
    public static function createNotification(array $data): self
    {
        return self::create([
            'type' => $data['type'] ?? self::TYPE_SYSTEM,
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'priority' => $data['priority'] ?? self::PRIORITY_NORMAL,
            'category' => $data['category'] ?? self::CATEGORY_GENERAL,
            'is_global' => $data['is_global'] ?? false,
            'target_roles' => $data['target_roles'] ?? null,
            'target_users' => $data['target_users'] ?? null,
            'scheduled_at' => isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}
