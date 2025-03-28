<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     */
    public array $reset_password_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    final public function __construct(array $reset_password_data)
    {
        $this->reset_password_data = $reset_password_data;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    final public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password of Your Grace Account',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    final public function content(): Content
    {
        return new Content(
            markdown: RESET_PASSWORD_EMAIL,
            with:     $this->reset_password_data,
        );
    }
}
