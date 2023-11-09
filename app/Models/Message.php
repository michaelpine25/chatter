<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'conversation_id',
        'image_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
