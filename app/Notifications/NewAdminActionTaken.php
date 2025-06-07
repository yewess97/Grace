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
        $action_in_past = $this->action === ADD 
            ? ADD.'ed' 
            : "{$this->action}d";

        $message = $this->isMultiple 
                ? "Multiple ".capitalizeAll($this->model[0]->getTable())." have been {$action_in_past}" 
                : capitalizeAll(singularize($this->model[0]->getTable()))." named *{$this->model[1]}* has been {$action_in_past}";

        return [
            'message' => "{$message} through ".auth()->user()?->{FULL_NAME}." (".ucfirst(ADMIN).").",
        ];
    }
}
