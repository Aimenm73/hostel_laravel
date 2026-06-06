<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'room_id', 'roll_no', 'department', 'year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
