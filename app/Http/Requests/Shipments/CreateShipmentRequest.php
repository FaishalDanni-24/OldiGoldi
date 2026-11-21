<?php

namespace App\Http\Requests\Shipments;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,order_id'],
            'courier_company' => ['required', 'string', 'max:60'],
            'courier_type' => ['required', 'string', 'max:60'],
            'delivery_type' => ['sometimes', 'in:now,scheduled'],
            'delivery_date' => ['sometimes', 'nullable', 'date'],
            'delivery_time' => ['sometimes', 'nullable', 'string', 'max:10'],
            'courier_insurance' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'order_note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
