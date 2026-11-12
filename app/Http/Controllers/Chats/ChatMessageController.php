<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Models\Chats\ChatMessage;
use App\Models\Chats\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Conversation $conversation): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($conversation->customer_id !== $user->user_id && $conversation->admin_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('sent_at')
            ->paginate(request()->integer('per_page', 50));

        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        if (! $user || ($conversation->customer_id !== $user->user_id && $conversation->admin_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        if ($conversation->conversation_status !== Conversation::STATUS_OPEN) {
            return response()->json(['message' => 'Conversation is closed.'], 422);
        }

        $validated = $request->validate([
            'message_type' => ['sometimes', 'in:text,image,file,system'],
            'message_text' => ['required_if:message_type,text', 'nullable', 'string'],
            'attachment_url' => ['nullable', 'string', 'max:1000'],
        ]);

        $message = ChatMessage::create([
            'sender_id' => $user->user_id,
            'conversation_id' => $conversation->conversation_id,
            'message_type' => $validated['message_type'] ?? ChatMessage::TYPE_TEXT,
            'message_text' => $validated['message_text'] ?? null,
            'attachment_url' => $validated['attachment_url'] ?? null,
            'is_read' => false,
        ]);

        $conversation->touch();

        return response()->json(['data' => $message->load('sender')], 201);
    }

    public function markRead(ChatMessage $message): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($message->conversation->customer_id !== $user->user_id && $message->conversation->admin_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        $message->update(['is_read' => true]);

        return response()->json(['data' => $message->fresh()]);
    }
}
