<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessFeedback extends Model
{
    protected $fillable = ['student_id', 'meal_date', 'meal_type', 'rating', 'comment'];

    protected function casts(): array
    {
        return ['meal_date' => 'date'];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
