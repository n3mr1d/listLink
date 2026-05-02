<?php

namespace App\Mail;

use App\Models\Advertisement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdvertiseSubmittedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Advertisement $ad)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name') . '] New Ad Request — ' . $this->ad->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.advertise-admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
