<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
    	$users = User::where('id', '<>', $request->user()->id)
    				->select(['id', 'name'])
    				->get();

    	return response()->json($users, 200);
    }
}
