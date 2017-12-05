<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chat\Chatroom;
use App\Models\User;
use App\Http\Requests\Chat\CreateChatroomRequest;
use Notification;
use App\Notifications\Chat\ChatroomCreated;
use App\Notifications\Chat\MemberAdded;
use App\Notifications\Chat\MemberRemoved;
use DB;

class ChatroomController extends Controller
{
    public function index(Request $request)
    {
    	$chatrooms = $request->user()->chatrooms()->latest()->get();
        $output = [];

        foreach ($chatrooms as $chatroom) {
            $output[] = array_merge(
                $chatroom->toArray(),
                ['unread_messages' => $request->user()->countUnreadMessages($chatroom->id)]
            );
        }

    	return response()->json($output, 200);
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

    public function availableUsers(Chatroom $chatroom)
    {
        $users = User::whereNotExists(function($query) use ($chatroom) {
            $query->select(DB::raw(1))
                ->from('chatroom_user')
                ->whereRaw('user_id = users.id')
                ->where('chatroom_id', $chatroom->id);
        })
        ->get();
        return response()->json($users, 200);
    }

    public function addMembers(Chatroom $chatroom, Request $request)
    {
        $members = $request->members;
        $member_users = [];

        if ($members) {
            $chatroom->users()->syncWithoutDetaching($members);

            $current_members = $chatroom->users()->get()->filter(function($user) use ($request){
                return $user->id <> $request->user()->id;
            });

            $users = $chatroom->users()->whereIn('user_id', $members)->get();
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

            Notification::send($current_members, new MemberAdded($chatroom, $member_users));
        }

        return response()->json($member_users, 200);
    }

    public function removeMember(Chatroom $chatroom, Request $request)
    {
        if ($request->user_id && $request->user()->canChangePermission($chatroom->id)) {
            $current_members = $chatroom->users()->get()->filter(function($user) use ($request){
                return $user->id <> $request->user()->id;
            });

            $chatroom->users()->detach($request->user_id);
            Notification::send($current_members, new MemberRemoved($request->user_id));
        }
        return response()->json('', 200);
    }

    public function ping(Chatroom $chatroom, Request $request)
    {
        $request->user()->unreadNotifications()
                    ->where('type', 'App\Notifications\Chat\MessageCreated')
                    ->where('data', 'like', '%"chatroom_id":"'.$chatroom->id.'"%')
                    ->delete();

        return response()->json('', 200);
    }
}
