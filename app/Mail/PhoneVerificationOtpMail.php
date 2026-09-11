<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PhoneVerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $otp)
    {
    }

    public function build(): self
    {
        return $this->subject('GasPOA - Thibitisha namba yako ya simu')
            ->view('emails.phone-verification-otp');
    }
}
