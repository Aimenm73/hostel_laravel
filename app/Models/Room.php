<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'number', 'floor', 'type', 'capacity', 'status',
    ];

    public function studentDetails()
    {
        return $this->hasMany(StudentDetail::class);
    }

    /**
     * Alias used by heat-map API.
     */
    public function students()
    {
        return $this->hasMany(StudentDetail::class);
    }

    public function occupantsCount()
    {
        return $this->studentDetails()->count();
    }
}
