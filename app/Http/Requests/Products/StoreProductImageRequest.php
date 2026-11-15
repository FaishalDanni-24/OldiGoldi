<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'image' => ['required_without:image_url', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['required_without:image', 'nullable', 'string', 'max:1000'],
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
