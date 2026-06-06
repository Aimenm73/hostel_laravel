<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'complaint_id', 'sender_id', 'message',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
