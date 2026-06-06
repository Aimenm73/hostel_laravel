<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'title', 'description', 'category', 'priority', 'status', 'image', 'reply', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class);
    }
}
