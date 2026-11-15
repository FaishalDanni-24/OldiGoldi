<?php

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,product_id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
