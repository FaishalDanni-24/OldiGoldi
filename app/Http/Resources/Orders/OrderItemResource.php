<?php

namespace App\Http\Resources\Orders;

use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_item_id' => $this->order_item_id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'product_name_snapshot' => $this->product_name_snapshot,
            'sku_snapshot' => $this->sku_snapshot,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'line_total' => $this->line_total,
        ];
    }
}
