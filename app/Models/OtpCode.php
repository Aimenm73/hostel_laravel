<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpCode extends Model
{
    protected $fillable = [
        'user_id', 'code', 'type', 'is_used', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used'    => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Generate a new 6-digit OTP for a user.
     */
    public static function generate(int $userId, string $type = 'login', int $minutesTTL = 5): self
    {
        // Invalidate any previous unused OTPs
        static::where('user_id', $userId)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        return static::create([
            'user_id'    => $userId,
            'code'       => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type'       => $type,
            'expires_at' => Carbon::now()->addMinutes($minutesTTL),
        ]);
    }
}
