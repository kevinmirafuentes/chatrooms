<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Chat\Chatroom;
use App\Models\Chat\Message;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function owned_chatrooms()
    {
        return $this->hasMany(Chatroom::class);
    }

    public function chatrooms()
    {
        return $this->belongsToMany(Chatroom::class)
                ->withPivot('permission')
                ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function canJoinRoom($id)
    {
        return $this->chatrooms()->where('chatroom_id', $id)->count() > 0;
    }

    public function canChangePermission($chatroom_id)
    {
        return $this->owned_chatrooms()->where('id', $chatroom_id)->count() > 0;
    }

    public function canWriteMessage(Chatroom $chatroom)
    {
        $chatroom_user = $chatroom->users()->where('user_id', $this->id)->first();
        if ($chatroom_user) {
            $permission = $chatroom_user->pivot->permission;
            return Chatroom::COLLABORATOR_PERMISSION === $permission;
        }
        return false;
    }

    public function countUnreadMessages($chatroom_id)
    {
        return $this->unreadNotifications()
                    ->where('type', 'App\Notifications\Chat\MessageCreated')
                    ->where('data', 'like', '%"chatroom_id":"'.$chatroom_id.'"%')
                    ->count();
    }
}
