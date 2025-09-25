<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value, string $type = 'string', string $group = 'general', string $description = null): bool
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        return $setting->exists;
    }

    /**
     * Cast value based on type
     */
    public static function castValue($value, string $type)
    {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Get reservation-specific settings
     */
    public static function getReservationSettings(): array
    {
        return self::getByGroup('reservation');
    }

    /**
     * Get default reservation notes
     */
    public static function getDefaultReservationNotes(): string
    {
        return self::getValue('default_reservation_notes', '');
    }

    /**
     * Get default deposit percentage
     */
    public static function getDefaultDepositPercentage(): float
    {
        return self::getValue('default_deposit_percentage', 0);
    }

    /**
     * Get default minimum deposit amount
     */
    public static function getDefaultMinimumDepositAmount(): float
    {
        return self::getValue('default_minimum_deposit_amount', 0);
    }

    /**
     * Get minimum reservation days
     */
    public static function getMinimumReservationDays(): int
    {
        return (int) self::getValue('minimum_reservation_days', 1);
    }
}
