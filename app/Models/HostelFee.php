<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelFee extends Model
{
    protected $fillable = [
        'student_id', 'title', 'category', 'amount', 'due_date', 'status', 'paid_at', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
