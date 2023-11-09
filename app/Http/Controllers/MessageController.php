<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function list(Request $request, $conversationId) {

        $userId = Auth::user()->id;
        $conversation = Conversation::where('id', $conversationId)->first();

        $messages = [];

        $messages = Message::where('conversation_id', $conversationId)
        ->with('user')
        ->get();

        return $messages;
    }

    public function store(Request $request, $conversationId) {

        $userId = Auth::user()->id;
        $content = $request->input('content');
        $image = $request->file('image');

        if($image) {
            $imagePath = $image->store('PictureMessages');
            $message = Message::create([
                'user_id' => $userId,
                'conversation_id' => $conversationId,
                'content' => $content,
                'image_url' => $imagePath,
            ]);
        } else {
            $message = Message::create([
                'user_id' => $userId,
                'conversation_id' => $conversationId,
                'content' => $content,
                'image_url' => null,
            ]);
        }
        
        $participants = Participant::where('conversation_id', $conversationId)->get();
        foreach ($participants as $participant) {
            if ($participant->user_id !== $userId) {
                $participant->unread_count += 1;
                $participant->save(); 
            }
        }  
        $message->load('user');

        event(new MessageSent($conversationId, $message));

        return response()->json(['message' => $message]);
    }
}
