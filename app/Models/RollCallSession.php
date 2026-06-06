<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RollCallSession extends Model
{
    protected $fillable = ['session_date', 'title', 'qr_token', 'status', 'opened_by', 'closed_at'];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public static function generateToken(): string
    {
        return Str::random(48);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
