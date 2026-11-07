<?php

namespace App\Models\Chats;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sender_id',
    'conversation_id',
    'message_type',
    'message_text',
    'attachment_url',
    'is_read',
    'sent_at'
])]
class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $primaryKey = 'message_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }
}
