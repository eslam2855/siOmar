<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'batch_uuid',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Get the reservation if this log is related to a reservation
     */
    public function reservation()
    {
        if ($this->subject_type === Reservation::class) {
            return $this->belongsTo(Reservation::class, 'subject_id');
        }
        return null;
    }

    /**
     * Get the unit if this log is related to a unit
     */
    public function unit()
    {
        if ($this->subject_type === Unit::class) {
            return $this->belongsTo(Unit::class, 'subject_id');
        }
        return null;
    }

    /**
     * Scope to filter by log name
     */
    public function scopeInLog($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope to filter by event
     */
    public function scopeEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by causer (user who performed action)
     */
    public function scopeCausedBy($query, $causer)
    {
        return $query->where('causer_type', get_class($causer))
                    ->where('causer_id', $causer->id);
    }

    /**
     * Scope to filter by subject (model that was affected)
     */
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->id);
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        return $this->description;
    }

    /**
     * Get human readable event name
     */
    public function getEventNameAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->event ?? ''));
    }

    /**
     * Get formatted properties
     */
    public function getFormattedPropertiesAttribute(): array
    {
        return $this->properties ?? [];
    }
}
