<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessMenu extends Model
{
    protected $fillable = ['day_of_week', 'breakfast', 'lunch', 'dinner'];

    public static function dayLabels(): array
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }

    public function getDayLabelAttribute(): string
    {
        return self::dayLabels()[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }
}
