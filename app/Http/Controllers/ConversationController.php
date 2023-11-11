<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{

    public function store(Request $request) {

       $user1Id = Auth::user()->id;
       $name = $request->input('name');

        $conversation = Conversation::create([
            'owner_id' => $user1Id,
            'name' => $name,
        ]);

        Participant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user1Id,
            'unread_count' => 0,
            'status' => 'allowed',
            'admin' => true
        ]);
        
        return response()->json(['conversation' => $conversation], 200);
    }

    public function list(Request $request) {
        $currentUser = Auth::user();
        $conversationList = $currentUser->conversations()->with(['lastMessage.user', 'participants' => function($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        }])->get();
    
        return $conversationList;
    }    

    public function updateConversationPhoto(Request $request, $conversationId) 
    {
        
        $conversation = Conversation::where('id', $conversationId)->first();

        if ($conversation->conversation_photo) {
            Storage::delete($conversation->conversation_photo);
        }

        if ($request->hasFile('conversation_photo')) {
            $conversationPhoto = $request->file('conversation_photo');
    
            // Store the uploaded file and get its path
            $path = $conversationPhoto->store('ConversationPhotos');
    
            // Update the user's profile_picture column with the path
            $conversation->update(['conversation_photo' => $path]);
    
            return response()->json(['message' => 'Conversation photo updated succesfully at ' . $conversationId], 200);
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
    }

    public function authorizeUser(Request $request, $conversationId) 
    {
        $currentUserId = Auth::user()->id;
        $conversation = Conversation::find($conversationId);
        $participants = $conversation->participants;

        foreach($participants as $participant) {
            if($participant->user_id == $currentUserId) {
                return true;
            }
        }

        return false;
    }
}
