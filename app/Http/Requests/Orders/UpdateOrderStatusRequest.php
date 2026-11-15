<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending_payment,paid,processing,shipped,delivered,completed,cancelled,refunded'],
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
