<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAdminActionTaken extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    final public function __construct(protected array $model, protected string $action, protected bool $isMultiple = false){}

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
        $message = $this->isMultiple
            ? "Multiple ".capitalizeAll($this->model[0]->getTable())." have been ".toPastTense($this->action)
            : capitalizeAll(singularize($this->model[0]->getTable()))." named *{$this->model[1]}* has been ".toPastTense($this->action);

        return [
            'message' => "{$message} through ".auth()->user()?->{FULL_NAME}." (".ucfirst(ADMIN).").",
        ];
    }
}
