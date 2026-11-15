<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chats\StoreConversationRequest;
use App\Http\Resources\Chats\ConversationResource;
use App\Models\Chats\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $conversations = Conversation::query()
            ->when($user->role?->role_name !== 'admin', fn ($q) => $q->where('customer_id', $user->user_id))
            ->with(['customer', 'admin', 'order', 'product', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('updated_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json(ConversationResource::collection($conversations));
    }

    public function store(StoreConversationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $existing = Conversation::where('customer_id', $user->user_id)
            ->where('conversation_status', Conversation::STATUS_OPEN)
            ->when(! empty($validated['admin_id']), fn ($q) => $q->where('admin_id', $validated['admin_id']))
            ->when(! empty($validated['product_id']), fn ($q) => $q->where('product_id', $validated['product_id']))
            ->when(! empty($validated['order_id']), fn ($q) => $q->where('order_id', $validated['order_id']))
            ->first();

        if ($existing) {
            return response()->json(['data' => new ConversationResource($existing)]);
        }

        $conversation = Conversation::create([
            'customer_id' => $user->user_id,
            'admin_id' => $validated['admin_id'] ?? null,
            'order_id' => $validated['order_id'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'conversation_status' => Conversation::STATUS_OPEN,
        ]);

        return response()->json(['data' => new ConversationResource($conversation)], 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($conversation->customer_id !== $user->user_id && $conversation->admin_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        return response()->json(['data' => new ConversationResource($conversation->load(['customer', 'admin', 'order', 'product', 'messages']))]);
    }

    public function close(Conversation $conversation): JsonResponse
    {
        $conversation->conversation_status = Conversation::STATUS_CLOSED;
        $conversation->save();

        return response()->json(['data' => new ConversationResource($conversation->fresh())]);
    }
}
