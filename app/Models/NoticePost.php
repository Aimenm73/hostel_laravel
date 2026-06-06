<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticePost extends Model
{
    protected $fillable = ['user_id', 'floor', 'title', 'body', 'is_pinned'];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(NoticeComment::class)->orderBy('created_at');
    }
}
