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
}
