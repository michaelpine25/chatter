<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'unread_count',
        'status',
        'admin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
