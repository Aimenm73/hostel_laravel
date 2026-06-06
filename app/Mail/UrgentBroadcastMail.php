<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UrgentBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $broadcastMessage,
        public string $broadcastType
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[URGENT] COMSATS Hostel Broadcast',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.urgent_broadcast',
        );
    }
}
