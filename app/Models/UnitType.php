<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
