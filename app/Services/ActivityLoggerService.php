<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLoggerService
{
    /**
     * Log an activity
     */
    public static function log(
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        array $properties = [],
        string $event = null,
        string $logName = null
    ): ActivityLog {
        return ActivityLog::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'causer_type' => $causer ? get_class($causer) : (Auth::user() ? get_class(Auth::user()) : null),
            'causer_id' => $causer ? $causer->id : Auth::id(),
            'properties' => $properties,
            'event' => $event,
            'batch_uuid' => null,
        ]);
    }

    /**
     * Log reservation status change
     */
    public static function logReservationStatusChange(
        Model $reservation,
        string $oldStatus,
        string $newStatus,
        ?string $adminNotes = null
    ): ActivityLog {
        $properties = [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'admin_notes' => $adminNotes,
            'reservation_number' => $reservation->reservation_number,
        ];

        return self::log(
            "Reservation status changed from {$oldStatus} to {$newStatus}",
            $reservation,
            Auth::user(),
            $properties,
            'status_changed',
            'reservations'
        );
    }

    /**
     * Log deposit verification
     */
    public static function logDepositVerification(Model $reservation): ActivityLog
    {
        $properties = [
            'deposit_amount' => $reservation->transfer_amount,
            'reservation_number' => $reservation->reservation_number,
        ];

        return self::log(
            "Deposit verified for reservation #{$reservation->reservation_number}",
            $reservation,
            Auth::user(),
            $properties,
            'deposit_verified',
            'reservations'
        );
    }

    /**
     * Log reservation creation
     */
    public static function logReservationCreated(Model $reservation): ActivityLog
    {
        $properties = [
            'reservation_number' => $reservation->reservation_number,
            'total_amount' => $reservation->total_amount,
            'guest_name' => $reservation->guest_name,
        ];

        return self::log(
            "New reservation created: #{$reservation->reservation_number}",
            $reservation,
            $reservation->user,
            $properties,
            'created',
            'reservations'
        );
    }

    /**
     * Log reservation update
     */
    public static function logReservationUpdated(Model $reservation): ActivityLog
    {
        $properties = [
            'reservation_number' => $reservation->reservation_number,
            'total_amount' => $reservation->total_amount,
            'guest_name' => $reservation->guest_name,
        ];

        return self::log(
            "Reservation updated: #{$reservation->reservation_number}",
            $reservation,
            $reservation->user,
            $properties,
            'updated',
            'reservations'
        );
    }

    /**
     * Log unit creation
     */
    public static function logUnitCreated(Model $unit): ActivityLog
    {
        $properties = [
            'unit_name' => $unit->name,
            'unit_number' => $unit->unit_number,
        ];

        return self::log(
            "New unit created: {$unit->name}",
            $unit,
            Auth::user(),
            $properties,
            'created',
            'units'
        );
    }

    /**
     * Log unit update
     */
    public static function logUnitUpdated(Model $unit, array $changes): ActivityLog
    {
        $properties = [
            'unit_name' => $unit->name,
            'changes' => $changes,
        ];

        return self::log(
            "Unit updated: {$unit->name}",
            $unit,
            Auth::user(),
            $properties,
            'updated',
            'units'
        );
    }

    /**
     * Log user action
     */
    public static function logUserAction(
        string $action,
        ?Model $subject = null,
        array $properties = []
    ): ActivityLog {
        return self::log(
            $action,
            $subject,
            Auth::user(),
            $properties,
            'user_action',
            'system'
        );
    }

    /**
     * Log admin action
     */
    public static function logAdminAction(
        string $action,
        ?Model $subject = null,
        array $properties = []
    ): ActivityLog {
        return self::log(
            $action,
            $subject,
            Auth::user(),
            $properties,
            'admin_action',
            'admin'
        );
    }

    /**
     * Get recent activities
     */
    public static function getRecentActivities(int $limit = 50)
    {
        return ActivityLog::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific model
     */
    public static function getActivitiesForModel(Model $model, int $limit = 20)
    {
        return ActivityLog::with(['causer'])
            ->where('subject_type', get_class($model))
            ->where('subject_id', $model->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by user
     */
    public static function getActivitiesByUser(Model $user, int $limit = 20)
    {
        return ActivityLog::with(['subject'])
            ->where('causer_type', get_class($user))
            ->where('causer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
