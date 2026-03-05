<?php

namespace App\Comment\Http\Requests;

use App\Comment\Database\Models\Comment;
use App\Post\Http\Controllers\PostPublicController;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
                function (string $attribute, mixed $value, \Closure $fail) use ($post): void {
                    if ($value !== null && $post !== null) {
                        if (! $post instanceof \App\Post\Database\Models\Post) {
                            return;
                        }
                        if (! is_numeric($value)) {
                            return;
                        }
                        $comment = Comment::find((int) $value);
                        if ($comment === null || $comment->post_id !== $post->id) {
                            $fail(__('The selected comment is invalid.'));

                            return;
                        }
                        $depth = 0;
                        $current = $comment;
                        while ($current->parent_id !== null) {
                            $depth++;
                            $current = $current->parent;
                        }
                        if ($depth >= PostPublicController::MAX_COMMENT_NESTING_DEPTH) {
                            $fail(__('Maximum nesting level reached. You cannot reply to this comment.'));
                        }
                    }
                },
            ],
        ];
    }
}
