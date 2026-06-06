<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    public $timestamps = false;

    protected $fillable = ['roll_call_session_id', 'student_id', 'method', 'marked_at'];

    protected function casts(): array
    {
        return ['marked_at' => 'datetime'];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function session()
    {
        return $this->belongsTo(RollCallSession::class, 'roll_call_session_id');
    }

    public function rollCallSession()
    {
        return $this->belongsTo(RollCallSession::class, 'roll_call_session_id');
    }
}
