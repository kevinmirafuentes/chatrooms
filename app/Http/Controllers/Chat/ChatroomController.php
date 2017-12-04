<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat\Chatroom;
use App\Models\User;
use App\Http\Requests\Chat\CreateChatroomRequest;
use Notification;
use App\Notifications\Chat\ChatroomCreated;

class ChatroomController extends Controller
{
    public function index(Request $request)
    {
    	$chatrooms = $request->user()->chatrooms()->latest()->get();
    	return response()->json($chatrooms, 200);
    }

    public function store(CreateChatroomRequest $request)
    {
    	$chatroom = $request->user()->owned_chatrooms()->create(['name' => $request->name]);
    	$users = $request->users;
    	$users[] = $request->user()->id;
    	$chatroom->users()->attach((array) $users);

        $users_models = User::whereIn('id', (array) $request->users)->get();
        if ($users_models) {
            Notification::send($users_models, new ChatroomCreated($chatroom));
        }

        $users = $request->users;
    	return response()->json($chatroom, 200);
    }
}
