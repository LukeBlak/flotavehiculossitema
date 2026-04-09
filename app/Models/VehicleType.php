<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'maintenance_interval_km',
        'tank_capacity',
        'payload_capacity',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
