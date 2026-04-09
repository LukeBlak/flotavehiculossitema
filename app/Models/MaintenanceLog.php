<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenanceLogFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'type',
        'description',
        'cost',
        'service_date',
        'next_due_km',
        'workshop_name',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
