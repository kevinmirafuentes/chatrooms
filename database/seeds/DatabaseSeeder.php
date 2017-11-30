<?php

use Illuminate\Database\Seeder;

use App\Models\Chat\Chatroom;
use App\Models\User;
use App\Models\Chat\Message;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Chatroom::class, 100)->create()->each(function($chatroom) {
			factory(User::class, 5)->create()->each(function($user) use ($chatroom) {
				$user->chatrooms()->attach($chatroom->id);
				$message = factory(Message::class)->make();
				$message->user_id = $user->id;
				$message->chatroom_id = $chatroom->id;
				$message->save();
			});
		});
    }
}
