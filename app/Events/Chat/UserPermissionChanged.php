<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserPermissionChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $permission;

    public $chatroom;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($permission, $chatroom)
    {
        $this->permission = $permission;
        $this->chatroom = $chatroom;
    }

    public function broadcastWith()
    {
        return [
            'permission' => $this->permission,
            'chatroom' => $this->chatroom,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chat.'.$this->chatroom);
    }
}
