<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat\Chatroom;
use App\Models\User;

class ChatroomController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function index(Request $request)
    {
    	$chatrooms = $request->user()->chatrooms()->get();
    	return response()->json($chatrooms, 200);
    }

    public function messages(Chatroom $chatroom, Request $request)
    {
    	$messages = $chatroom
                        ->messages()
                        ->with('user')
                        ->latest()
                        ->paginate();

        $results = [];
        foreach ($messages as $message) {
            $results[] = [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at,
                'self_owned' => $message->selfOwned,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'first_name' => $message->user->first_name,
                    'last_name' => $message->user->last_name,
                ]
            ];
        }

    	return response()->json([
            'messages' => $results,
            'next_page_url' => $messages->nextPageUrl(),
        ], 200);
    }
}
