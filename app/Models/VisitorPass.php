<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorPass extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'visitor_name', 'relationship', 'visit_date',
        'expected_arrival', 'purpose', 'status', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'created_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
