<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeComment extends Model
{
    protected $fillable = ['notice_post_id', 'user_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(NoticePost::class, 'notice_post_id');
    }
}
