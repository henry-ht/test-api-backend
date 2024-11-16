<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new notification instance.
     */
    public function __construct(private Array $data, private User $toUser)
    {
        $this->afterCommit();
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'broadcast.'.$this->data["type"];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'broadcast',
            'database'
        ];
    }

    public function dataArray() : array {
        return [
            "type"      => $this->data["type"],
            "data"      => $this->data,
            "user_id"   => $this->toUser->id,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->dataArray();
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage([
            "type"      => $this->data["type"],
            "data"      => $this->data,
            "user_id"   => $this->toUser->id,
            "sale_id"   => $this->data["sale"]->id,
        ]));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "notification";
        switch ($subject) {
            case 'message':
                $subject = __("You were answered in")." ".$this->data["sale"]->products[0]->name;
                break;
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($this->toUser->name.", ".__("You received a response"))
                    ->line($this->data["type"] == "message", __("You asked in")." ".$this->data["sale"]->products[0]->name)
                    ->line(__('Thank you for using our application!'));
    }
}
