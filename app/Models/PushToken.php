<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'device_id',
        'app_version',
        'device_info',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // Platforms
    const PLATFORM_IOS = 'ios';
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_WEB = 'web';

    /**
     * Get the user that owns the push token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for platform-specific tokens
     */
    public function scopeForPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope for user tokens
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): bool
    {
        return $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate token
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Activate token
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Check if token is valid and active
     */
    public function isValid(): bool
    {
        return $this->is_active && !empty($this->token);
    }

    /**
     * Get platform icon
     */
    public function getPlatformIconAttribute(): string
    {
        return match($this->platform) {
            self::PLATFORM_IOS => 'apple',
            self::PLATFORM_ANDROID => 'android',
            self::PLATFORM_WEB => 'globe',
            default => 'mobile',
        };
    }

    /**
     * Get platform color
     */
    public function getPlatformColorAttribute(): string
    {
        return match($this->platform) {
            self::PLATFORM_IOS => 'gray',
            self::PLATFORM_ANDROID => 'green',
            self::PLATFORM_WEB => 'blue',
            default => 'gray',
        };
    }
}
