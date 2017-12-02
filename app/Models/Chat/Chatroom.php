<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Chatroom extends Model
{
	const COLLABORATOR_PERMISSION = 0;
	const READONLY_PERMISSION = 1;

	public $incrementing = false;

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
		return $this->belongsToMany(User::class)
					->withPivot('permission')
					->withTimestamps();
	}

	public function messages()
	{
		return $this->hasMany(Message::class);
	}
}
