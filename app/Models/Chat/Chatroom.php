<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Chatroom extends Model
{
	public $incrementing = false;

    // identifies if group chat or single

	protected $fillable = [
		'name'
	];

	protected $appends = [
		'selfOwned',
		'isGroupChat'
	];

	public static function boot()
	{
		parent::boot();

		static::creating(function($table) {
			while ($id = uniqid()) {
				if (static::where('id', $id)->count() == 0) {
					$table->id = uniqid();
					break;
				}
			}
		});
	}

	public function getSelfOwnedAttribute()
	{
		return $this->user_id === auth()->user()->id;
	}

	public function getIsGroupChatAttribute()
	{
		return $this->users()->count() > 2;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	public function messages()
	{
		return $this->hasMany(Message::class);
	}
}
