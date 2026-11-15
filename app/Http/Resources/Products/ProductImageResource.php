<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'image_id' => $this->image_id,
            'product_id' => $this->product_id,
            'image_url' => $this->image_url,
            'is_primary' => $this->is_primary,
            'sort_order' => $this->sort_order,
        ];
    }
}
