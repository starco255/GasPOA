<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $otp)
    {
    }

    public function build(): self
    {
        return $this->subject('GasPOA - Namba ya kubadilisha nywila')
            ->view('emails.password-reset-otp');
    }
}
