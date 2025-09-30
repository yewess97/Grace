<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    final public function __construct(protected Order $order){}

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    final public function via(): array
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
            'message' => "New ".ORDER_MODEL." #{$this->{ORDER_MODEL}->tracking_num} has been ".toPastTense('place')." by {$this->{ORDER_MODEL}->{USER_MODEL}->fullName}",
        ];
    }
}
