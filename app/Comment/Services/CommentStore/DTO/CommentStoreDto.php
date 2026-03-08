<?php

namespace App\Comment\Services\CommentStore\DTO;

use App\Comment\Database\Models\Comment;
use App\Comment\Http\Requests\CommentStoreRequest;
use App\Post\Database\Models\Post;
use InvalidArgumentException;
use RuntimeException;
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
            throw new RuntimeException('Authenticated user is required.');
        }

        $content = $request->validated('content');
        if (! is_string($content)) {
            throw new InvalidArgumentException('Content must be a string.');
        }

        $parentUuid = $request->validated('parent_uuid');
        $parentId = null;
        if (is_string($parentUuid) && $parentUuid !== '') {
            $res = Comment::where('uuid', $parentUuid)->value('id');
            if (is_int($res)) {
                $parentId = $res;
            } elseif (is_string($res) && ctype_digit($res)) {
                $parentId = (int) $res;
            }
        }

        return new self(
            postId: $post->id,
            userId: $user->id,
            parentId: $parentId,
            content: $content,
        );
    }
}
