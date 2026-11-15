<?php

namespace App\Http\Resources\Chats;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'message_id' => $this->message_id,
            'sender_id' => $this->sender_id,
            'sender' => $this->whenLoaded('sender', fn () => new UserResource($this->sender)),
            'conversation_id' => $this->conversation_id,
            'message_type' => $this->message_type,
            'message_text' => $this->message_text,
            'attachment_url' => $this->attachment_url,
            'is_read' => $this->is_read,
            'sent_at' => $this->sent_at,
        ];
    }
}
