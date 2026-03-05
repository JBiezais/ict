<?php

namespace App\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation (convert empty strings to null for optional date fields).
     */
    protected function prepareForValidation(): void
    {
        $replace = [];
        if ($this->has('date_from') && $this->date_from === '') {
            $replace['date_from'] = null;
        }
        if ($this->has('date_to') && $this->date_to === '') {
            $replace['date_to'] = null;
        }
        if ($this->has('search') && is_string($this->search)) {
            $replace['search'] = trim(preg_replace('/\s+/', ' ', $this->search));
        }
        if ($replace !== []) {
            $this->merge($replace);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['sometimes', 'string', 'in:date,date_asc,comments,comments_asc'],
            'include_uncategorized' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'nullable', 'string', 'max:200'],
        ];
    }
}
