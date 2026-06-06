<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title', 'description', 'venue', 'date', 'time', 'max_seats', 'booked', 'image', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'registrations', 'event_id', 'student_id')
                    ->withPivot('registered_at', 'payment_status', 'payment_date');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function availableSeats()
    {
        return $this->max_seats - $this->booked;
    }
}
