<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        $id = $this->route('role')?->role_id;

        return [
            'role_name' => ['required', 'string', 'max:30', 'unique:roles,role_name,' . $id . ',role_id'],
        ];
    }
}
