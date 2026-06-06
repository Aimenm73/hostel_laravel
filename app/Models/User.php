<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'profile_pic', 'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function studentDetail()
    {
        return $this->hasOne(StudentDetail::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'student_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'registrations', 'student_id', 'event_id')
                    ->withPivot('registered_at', 'payment_status', 'payment_date');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'student_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getAvatarAttribute()
    {
        if ($this->profile_pic) {
            return asset('storage/profiles/' . $this->profile_pic);
        }
        return null;
    }

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
