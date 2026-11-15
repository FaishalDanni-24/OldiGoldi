<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'admin_id' => ['nullable', 'integer', 'exists:users,user_id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,order_id'],
            'product_id' => ['nullable', 'integer', 'exists:products,product_id'],
        ];
    }
}
