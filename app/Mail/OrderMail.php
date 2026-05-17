<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     * @return void
     */
    final public function __construct(protected Order $order){}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    final public function envelope(): Envelope
    {
        return new Envelope(
            bcc:     'yewess97@gmail.com',
            subject: 'Your Grace order has been placed successfully!',
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
            markdown: ORDER_EMAIL,
            with: [
                ...getOrderDetails($this->{ORDER_MODEL}),
                ORDER_USER_NAME => $this->{ORDER_MODEL}->{USER_MODEL}->{FULL_NAME},
                'logo' => storage_path('app/public/images/favicon.png'),
            ],
        );
    }
}
