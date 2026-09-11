<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
        $phone = $notifiable->phone_number;

        // For now, log the SMS content (later replace with real API call)
        Log::info("SMS sent to {$phone}: {$message}");
        
        // When you have a real SMS provider, use:
        // Http::post('https://api.sms-provider.com/send', [
        //     'to' => $phone,
        //     'message' => $message,
        // ]);
    }
}