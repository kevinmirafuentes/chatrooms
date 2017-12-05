<?php

namespace App\Notifications\Chat;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Chat\Chatroom;

class MemberAdded extends Notification
{
    use Queueable;

    public $chatroom;

    public $members;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Chatroom $chatroom, $members)
    {
        $this->chatroom = $chatroom;
        $this->members = $members;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'chatroom' => $this->chatroom->toArray(),
            'members' => $this->members,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
           'chatroom' => $this->chatroom->toArray(),
            'members' => $this->members,
        ]);
    }
}
