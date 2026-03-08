<?php

namespace App\Comment\Http\Requests;

use App\Comment\Http\Rules\ValidCommentDepthRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'content' => ['required', 'string', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id',
                new ValidCommentDepthRule($post),
            ],
        ];
    }
}
