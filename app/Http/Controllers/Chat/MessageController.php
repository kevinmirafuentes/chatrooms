<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat\Chatroom;
use App\Models\Chat\Message;
use App\Notifications\Chat\MessageCreated;
use Notification;

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
            $results[] = $message->formatForJson();
        }

        $users = $chatroom
                    ->users()
                    ->where('user_id', '<>', $request->user()->id)
                    ->get();

        $member_users = [];

        foreach ($users as $user) {
            $member_users[] = [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'permission' => $user->pivot->permission,
            ];
        }

        $permission = 0;

        $current_user = $chatroom
                            ->users()
                            ->where('user_id', '=', $request->user()->id)
                            ->first();

        $permission = $current_user->pivot->permission;

        return response()->json([
            'messages' => $results,
            'next_page_url' => $messages->nextPageUrl(),
            'members' => $member_users,
            'permission' => $permission,
        ], 200);
    }

    public function store(Chatroom $chatroom, Request $request)
    {
        if (!$request->user()->canWriteMessage($chatroom)) {
            return response()->json('Unauthorized.', 401);
        }

        $message = Message::create([
            'user_id' => $request->user()->id,
            'chatroom_id' => $chatroom->id,
            'body' => $request->body
        ]);

        $notifiables = $chatroom->users()->where('user_id', '<>', $message->user_id)->get();
        Notification::send($notifiables, new MessageCreated($message));

        return response()->json($message->formatForJson(), 200);
    }
}
