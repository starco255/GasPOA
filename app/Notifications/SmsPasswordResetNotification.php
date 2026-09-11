<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SmsPasswordResetNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        // Tutatumia custom channel "sms" (tutaunda baadaye)
        return ['sms'];
    }

 public function toSms($notifiable)
  {
    $url = url(route('password.reset', [
        'token' => $this->token,
        'email' => $notifiable->email, // or phone if needed
    ], false));

    return "GasPOA: Tumia link hii kurejesha nywila yako: $url";
}
}
