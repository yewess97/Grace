<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReviewAdded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Review $review){}

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    final public function toArray(): array
    {
        return [
            'message' => "New ".REVIEW_MODEL." added for *{$this->{REVIEW_MODEL}->{PRODUCT_MODEL}->name}* ".PRODUCT_MODEL." by {$this->{REVIEW_MODEL}->{USER_MODEL}->fullName}",
        ];
    }
}
