<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        $id = $this->route('category')?->category_id;

        return [
            'category_name' => ['sometimes', 'string', 'max:100', 'unique:categories,category_name,' . $id . ',category_id'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:120', 'unique:categories,slug,' . $id . ',category_id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
