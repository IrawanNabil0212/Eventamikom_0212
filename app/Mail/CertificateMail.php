<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public Event $event,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sertifikat Kehadiran - '.$this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
            with: [
                'participantName' => $this->transaction->customer_name,
                'eventTitle' => $this->event->title,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', $this->transaction->certificate_path)
                ->as('Sertifikat - '.$this->event->title.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}