<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
