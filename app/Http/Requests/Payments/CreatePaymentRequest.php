<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,order_id'],
            'provider' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
