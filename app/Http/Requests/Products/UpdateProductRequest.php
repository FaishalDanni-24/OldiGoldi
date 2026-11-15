<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        $id = $this->route('product')?->product_id;

        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,category_id'],
            'sku' => ['sometimes', 'string', 'max:60', 'unique:products,sku,' . $id . ',product_id'],
            'product_name' => ['sometimes', 'string', 'max:180'],
            'brand' => ['sometimes', 'nullable', 'string', 'max:100'],
            'model' => ['sometimes', 'nullable', 'string', 'max:120'],
            'product_description' => ['sometimes', 'nullable', 'string'],
            'condition_grade' => ['sometimes', 'in:like_new,good,fair,for_parts'],
            'condition_notes' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'product_status' => ['sometimes', 'in:draft,active,sold_out,inactive'],
        ];
    }
}
