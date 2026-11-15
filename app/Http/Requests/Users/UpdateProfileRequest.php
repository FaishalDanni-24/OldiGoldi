<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->user()?->user_id;

        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', 'max:150', 'unique:users,email,' . $userId . ',user_id'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:30'],
        ];
    }
}
