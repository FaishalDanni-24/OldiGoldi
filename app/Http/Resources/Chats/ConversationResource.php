<?php

namespace App\Http\Resources\Chats;

use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'conversation_id' => $this->conversation_id,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn () => new UserResource($this->customer)),
            'admin_id' => $this->admin_id,
            'admin' => $this->whenLoaded('admin', fn () => new UserResource($this->admin)),
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'conversation_status' => $this->conversation_status,
            'messages' => $this->whenLoaded('messages', fn () => ChatMessageResource::collection($this->messages)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
