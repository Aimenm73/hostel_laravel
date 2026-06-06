<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id', 'student_id', 'registered_at', 'payment_status', 'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'payment_date' => 'datetime',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
