<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'user_id' => $this->user_id,
            'seller' => $this->whenLoaded('seller', fn () => new \App\Http\Resources\Users\UserResource($this->seller)),
            'sku' => $this->sku,
            'product_name' => $this->product_name,
            'brand' => $this->brand,
            'model' => $this->model,
            'product_description' => $this->product_description,
            'condition_grade' => $this->condition_grade,
            'condition_notes' => $this->condition_notes,
            'price' => $this->price,
            'stock' => $this->stock,
            'product_status' => $this->product_status,
            'images' => $this->whenLoaded('images', fn () => ProductImageResource::collection($this->images)),
            'primary_image' => $this->whenLoaded('primaryImage', fn () => new ProductImageResource($this->primaryImage)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
