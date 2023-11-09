<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'conversation_photo'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->latest(); // Orders messages by created_at in descending order
    }
    
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest(); // Orders the latest message by created_at in descending order
    }
    
    public function participants() {

        return $this->hasMany(Participant::class, 'conversation_id');
        
    }


}
