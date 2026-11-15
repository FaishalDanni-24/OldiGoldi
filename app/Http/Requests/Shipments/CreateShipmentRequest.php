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
            'tracking_provider' => ['required', 'string', 'max:60'],
            'courier_code' => ['nullable', 'string', 'max:60'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:150'],
        ];
    }
}
