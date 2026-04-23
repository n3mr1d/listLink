<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;
    public string $verifyCode;
    public string $username;
    public bool   $isOnion;

    public function __construct(string $username, string $verifyUrl, string $verifyCode, bool $isOnion = false)
    {
        $this->username  = $username;
        $this->verifyUrl = $verifyUrl;
        $this->verifyCode = $verifyCode;
        $this->isOnion   = $isOnion;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name') . '] Verify Your Email Address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
