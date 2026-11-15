<?php

namespace App\Http\Resources\Carts;

use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->cart_id,
            'user_id' => $this->user_id,
            'cart_status' => $this->cart_status,
            'items' => $this->whenLoaded('items', fn () => CartItemResource::collection($this->items)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
