<?php

namespace App\Post\Services\PostUpdate\DTO;

use App\Post\Database\Models\Post;
use App\Post\Http\Requests\PostUpdateRequest;
use Spatie\LaravelData\Data;

class PostUpdateDto extends Data
{
    public function __construct(
        public readonly int $postId,
        public readonly string $title,
        public readonly string $content,
    ) {}

    public static function fromRequest(PostUpdateRequest $request, Post $post): self
    {
        $title = $request->validated('title');
        $content = $request->validated('content');
        if (! is_string($title) || ! is_string($content)) {
            throw new \InvalidArgumentException('Title and content must be strings.');
        }

        return new self(
            postId: $post->id,
            title: $title,
            content: $content,
        );
    }
}
