<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $userName;

    public function __construct(string $otpCode, string $userName)
    {
        $this->otpCode = $otpCode;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Verification Code - COMSATS Hostel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp_login',
        );
    }
}
