<?php

namespace App\Comment\Services\CommentStore\DTO;

use App\Comment\Http\Requests\CommentStoreRequest;
use App\Post\Database\Models\Post;
use Spatie\LaravelData\Data;

class CommentStoreDto extends Data
{
    public function __construct(
        public readonly int $postId,
        public readonly int $userId,
        public readonly ?int $parentId,
        public readonly string $content,
    ) {}

    public static function fromRequest(CommentStoreRequest $request, Post $post): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        $content = $request->validated('content');
        if (! is_string($content)) {
            throw new \InvalidArgumentException('Content must be a string.');
        }
        $parentId = $request->validated('parent_id');
        $parentId = $parentId !== null && is_numeric($parentId) ? (int) $parentId : null;

        return new self(
            postId: $post->id,
            userId: $user->id,
            parentId: $parentId,
            content: $content,
        );
    }
}
