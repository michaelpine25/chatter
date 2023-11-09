<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Events\InvitationSent;
use App\Events\InvitationAccepted;


class InvitationController extends Controller
{
    public function list(Request $request)
{
    $currentUser = Auth::user();

    $invitations = $currentUser->invitations()->with('sender', 'conversation')->get();

    foreach ($invitations as $invitation) {
        $sender = $invitation->sender;  // Access sender
        $conversation = $invitation->conversation;  // Access conversation

        return response()->json(['invitations' => $invitations]);
    }
}

    public function store(Request $request, $conversationId) {

        $currentUser = Auth::user();

        $recipientEmail = $request->input('recipientEmail');

        $recipientUser = User::where('email', $recipientEmail)->first();

        $conversationParticipants = Participant::where('conversation_id', $conversationId)->get();

        $invitees = Invitation::where('conversation_id', $conversationId)->get();

        if(!$recipientUser) {
            http_response_code(400);  // Set HTTP status code to 400 (Bad Request)
            echo json_encode(array("error" => $recipientEmail . ' does not have an account'));
            exit(); 
        }

        foreach ($conversationParticipants as $conversationParticipant) {
            if ($conversationParticipant->user_id == $recipientUser->id) {
                http_response_code(400);  // Set HTTP status code to 400 (Bad Request)
                echo json_encode(array("error" => $recipientEmail . ' is already in this conversation'));
                exit();  // Stop further execution
            }
        }    
        
        foreach ($invitees as $invitee) {
            if ($invitee->recipient_user_id == $recipientUser->id) {
                http_response_code(400);  // Set HTTP status code to 400 (Bad Request)
                echo json_encode(array("error" => $recipientEmail . ' has already been invited'));
                exit();  // Stop further execution
            }
        }   

        $invitation = Invitation::create([
            'sender_user_id' => $currentUser->id,
            'recipient_user_id' => $recipientUser->id,
            'conversation_id' => $conversationId
        ]);
        
        event(new InvitationSent($recipientUser));
    }

    public function accept(Request $request) {

        $currentUser = Auth::user();
        $conversationId = $request->input('conversationId');
        
        //add participant
        Participant::create([
            'conversation_id' => $conversationId,
            'user_id' => $currentUser->id,
            'unread_count' => 0,
            'status' => 'allowed',
            'admin' => false,
        ]);

        //delete invitaition after it has been accepted
        Invitation::where('conversation_id', $conversationId)
        ->where('recipient_user_id', $currentUser->id)
        ->delete();

        event(new InvitationAccepted($conversationId));
    }

    public function deny(Request $request, $conversationId) {
        $currentUser = Auth::user();

        Invitation::where('conversation_id', $conversationId)
        ->where('recipient_user_id', $currentUser->id)
        ->delete();

    }

}