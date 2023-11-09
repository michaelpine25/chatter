<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Participant;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{

    public function store(Request $request, $conversationId, $userId) {

        Participant::create([
            'conversation_id' => $conversationId,
            'user_id' => $user_id,
            'unread_count' => 0,
            'status' => 'allowed',
            'admin' => false
        ]);

    }

    public function list(Request $request, $conversationId) 
    {
        $participants = Participant::where('conversation_id', $conversationId)
            ->with('user')
            ->get();

        return response()->json(['participants' => $participants]);
    }

    public function resetUnreadCount(Request $request, $conversationId) 
    {
        $currentUserId = Auth::user()->id;

        $currentUserAsParticipant = Participant::where('conversation_id', $conversationId)
            ->where('user_id', $currentUserId)
            ->first(); 
        if ($currentUserAsParticipant) {
            $currentUserAsParticipant->unread_count = 0;
            $currentUserAsParticipant->save(); // Save the changes to the database
        } else {
            return response()->json(['error' => 'Participant not found for the given conversation'], 404);
        }
    }

    public function destroy(Request $request, $conversationId) 
    {
        $currentUserId = Auth::user()->id;

        $participant = Participant::where('conversation_id', $conversationId)
            ->where('user_id', $currentUserId)
            ->first();

        if ($participant) {
            $participant->delete();
            return response()->json(['message' => 'Participant deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Participant not found'], 404);
        }
    }
}