<?php

namespace App\Post\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class PostBrowseRequest extends PostFilterRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['sometimes', 'string', 'in:date,date_asc,comments,comments_asc'],
            'include_uncategorized' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'nullable', 'string', 'max:200'],
            'filter_applied' => ['sometimes', 'boolean'],
        ];
    }
}
