<?php

namespace App\Http\Controllers\chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'user_id' => 'required'
        ]);

        // Create a new message
        $message = Message::create([
            'user_id' => $request->user_id,
            'message' => $request->input('message'),
        ]);

        // broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => 'Message sent successfully'], 200);
    }
}
