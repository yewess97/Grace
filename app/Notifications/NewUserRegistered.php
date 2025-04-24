<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    final public function __construct(protected Model $user) {}

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
            'message' => "New ".USER_MODEL." *".capitalizeAll($this->{USER_MODEL}->{FULL_NAME})."* has ".REGISTER."ed",
        ];
    }
}
