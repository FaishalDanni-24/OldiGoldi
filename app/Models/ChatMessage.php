<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sender_id',
    'conversation_id',
    'message_type',
    'message_text',
    'attachment_url',
    'is_read',
    'sent_at'
])]
#[Table(key: 'message_id', timestamps: false)]
class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;
}
