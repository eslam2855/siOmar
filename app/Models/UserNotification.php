<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'notification_id',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'is_delivered',
        'delivered_at',
        'delivery_data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'is_delivered' => 'boolean',
        'delivered_at' => 'datetime',
        'delivery_data' => 'array',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent(array $deliveryData = []): bool
    {
        if (!$this->is_sent) {
            $this->update([
                'is_sent' => true,
                'sent_at' => now(),
                'delivery_data' => $deliveryData,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Mark notification as delivered
     */
    public function markAsDelivered(array $deliveryData = []): bool
    {
        if (!$this->is_delivered) {
            $this->update([
                'is_delivered' => true,
                'delivered_at' => now(),
                'delivery_data' => array_merge($this->delivery_data ?? [], $deliveryData),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    /**
     * Scope for delivered notifications
     */
    public function scopeDelivered($query)
    {
        return $query->where('is_delivered', true);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
