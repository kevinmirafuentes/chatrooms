<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    protected $fillable = [
    	'body',
        'user_id',
        'chatroom_id',
    ];

    protected $appends = [
		'selfOwned'
	];

    public function getSelfOwnedAttribute()
	{
		return $this->user_id === auth()->user()->id;
	}

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function chatroom()
    {
    	return $this->belongsTo(Chatroom::class);
    }

    public function formatForJson($for_other_users = false)
    {

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'self_owned' => $for_other_users ? false : $this->selfOwned,
            'sending' => false,
            'failed' => false,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ]
        ];
    }
}
