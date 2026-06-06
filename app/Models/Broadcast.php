<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'message', 'type', 'recipients_count', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
