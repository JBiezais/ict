<?php

namespace App\Comment\Http\Rules;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\Post\Http\Controllers\PostPublicController;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

readonly class ValidCommentDepthRule implements ValidationRule
{
    public function __construct(
        private mixed $post
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $this->post === null) {
            return;
        }

        if (! $this->post instanceof Post) {
            return;
        }

        if (! is_string($value) || $value === '') {
            return;
        }

        $comment = Comment::where('uuid', $value)->first();
        if ($comment === null || $comment->post_id !== $this->post->id) {
            $fail(__('The selected comment is invalid.'));

            return;
        }

        $depth = 0;
        $current = $comment;
        while ($current !== null && $current->parent_id !== null) {
            $depth++;
            $current = $current->parent;
        }

        if ($depth >= PostPublicController::MAX_COMMENT_NESTING_DEPTH) {
            $fail(__('Maximum nesting level reached. You cannot reply to this comment.'));
        }
    }
}
