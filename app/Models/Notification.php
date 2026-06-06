<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'message', 'is_read',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'is_read' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
