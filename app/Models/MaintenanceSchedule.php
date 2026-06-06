<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'floor', 'starts_at', 'ends_at', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function typeColors(): array
    {
        return [
            'water' => '#3b82f6',
            'power' => '#f59e0b',
            'wifi' => '#8b5cf6',
            'elevator' => '#ef476f',
            'general' => '#06d6a0',
        ];
    }
}
