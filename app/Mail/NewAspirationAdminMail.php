<?php

namespace App\Mail;

use App\Models\Aspiration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAspirationAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance mailable baru.
     */
    public function __construct(
        public Aspiration $aspiration
    ) {
    }

    /**
     * Dapatkan envelope pesan.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aspirasi Mahasiswa Baru: [' . $this->aspiration->ticket_number . '] ' . $this->aspiration->subject,
        );
    }

    /**
     * Dapatkan definisi konten pesan.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.aspirations.admin_notification',
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
