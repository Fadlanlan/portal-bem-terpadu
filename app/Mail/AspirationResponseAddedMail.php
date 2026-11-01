<?php

namespace App\Mail;

use App\Models\Aspiration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AspirationResponseAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public Aspiration $aspiration,
        public string $officialResponse
    ) {
    }

    /**
     * Dapatkan envelope pesan.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tanggapan Resmi BEM: [' . $this->aspiration->ticket_number . '] ' . $this->aspiration->subject,
        );
    }

    /**
     * Dapatkan definisi konten pesan.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.aspirations.response_added',
        );
    }

    /**
     * Dapatkan lampiran pesan.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
