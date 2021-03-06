<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Chat\Chatroom;
use App\Notifications\Chat\UserPermissionChanged;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('id', '<>', $request->user()->id)
                    ->select(['id', 'name'])
                    ->get();

        return response()->json($users, 200);
    }

    public function toReadonly(Request $request)
    {
        $users = (array) $request->users;
        $chatroom = $request->chatroom;

        if ($request->user()->canChangePermission($chatroom)) {
            $users = User::whereIn('id', $users)->get();
            foreach ($users as $user) {
                $user->chatrooms()->updateExistingPivot($chatroom, ['permission' => Chatroom::READONLY_PERMISSION]);

                $user->notify(new UserPermissionChanged($chatroom, Chatroom::READONLY_PERMISSION));
            }
        }

        return response('', 200);
    }

    public function toCollab(Request $request)
    {
        $users = (array) $request->users;
        $chatroom = $request->chatroom;

        if ($request->user()->canChangePermission($chatroom)) {
            $users = User::whereIn('id', $users)->get();
            foreach ($users as $user) {
                $user->chatrooms()->updateExistingPivot($chatroom, ['permission' => Chatroom::COLLABORATOR_PERMISSION]);

                $user->notify(new UserPermissionChanged($chatroom, Chatroom::COLLABORATOR_PERMISSION));
            }
        }
    }
}
