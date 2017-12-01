<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat\Chatroom;
use App\Models\Chat\Message;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Chatroom $chatroom, Request $request)
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
                'sending' => false,
                'failed' => false,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'first_name' => $message->user->first_name,
                    'last_name' => $message->user->last_name,
                ]
            ];
        }

        $member_users = $chatroom
                            ->users()
                            ->where('user_id', '<>', $request->user()->id)
                            ->select([
                                'users.id',
                                'users.name',
                                'users.first_name',
                                'users.last_name',
                                'users.email'
                            ])
                            ->get();

        return response()->json([
            'messages' => $results,
            'next_page_url' => $messages->nextPageUrl(),
            'members' => $member_users,
        ], 200);
    }

    public function store(Chatroom $chatroom, Request $request)
    {
        $message = Message::create([
            'user_id' => $request->user()->id,
            'chatroom_id' => $chatroom->id,
            'body' => $request->body
        ]);

        return response()->json($message, 200);
    }
}
