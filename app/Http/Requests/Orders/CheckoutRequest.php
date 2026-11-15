<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'exists:addresses,address_id'],
            'shipping_cost' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'recipient_name' => ['sometimes', 'string', 'max:120'],
            'recipient_phone' => ['sometimes', 'string', 'max:30'],
            'shipping_address_line' => ['sometimes', 'string', 'max:500'],
            'shipping_district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'shipping_city' => ['sometimes', 'string', 'max:100'],
            'shipping_province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['sometimes', 'nullable', 'string', 'max:15'],
            'customer_note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
