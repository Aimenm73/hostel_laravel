<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotifier
{
    public function send(User $user, string $message): void
    {
        $phone = $user->phone;
        if (!$phone) {
            return;
        }

        $webhook = config('services.sms.webhook_url');
        $status = 'logged';

        if ($webhook) {
            try {
                Http::timeout(5)->post($webhook, [
                    'phone' => $phone,
                    'message' => $message,
                ]);
                $status = 'sent';
            } catch (\Throwable $e) {
                Log::warning('SMS webhook failed: ' . $e->getMessage());
                $status = 'failed';
            }
        }

        SmsLog::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'message' => $message,
            'status' => $status,
        ]);
    }
}
