<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'sku' => ['required', 'string', 'max:60', 'unique:products,sku'],
            'product_name' => ['required', 'string', 'max:180'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:120'],
            'product_description' => ['nullable', 'string'],
            'condition_grade' => ['required', 'in:like_new,good,fair,for_parts'],
            'condition_notes' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'product_status' => ['sometimes', 'in:draft,active,sold_out,inactive'],
        ];
    }
}
