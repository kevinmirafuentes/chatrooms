<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chatroom;

class ChatController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth');
    }

    public function index()
    {
        return view('chat.chatroom');
    }
}
