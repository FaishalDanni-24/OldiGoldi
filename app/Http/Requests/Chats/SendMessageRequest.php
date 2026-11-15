<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'message_type' => ['sometimes', 'in:text,image,file,system'],
            'message_text' => ['required_if:message_type,text', 'nullable', 'string'],
            'attachment_url' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
