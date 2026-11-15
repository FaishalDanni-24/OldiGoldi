<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'address_label' => ['sometimes', 'nullable', 'string', 'max:50'],
            'recipient_name' => ['sometimes', 'string', 'max:120'],
            'recipient_phone' => ['sometimes', 'string', 'max:30'],
            'address_line' => ['sometimes', 'string', 'max:500'],
            'district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'string', 'max:100'],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
