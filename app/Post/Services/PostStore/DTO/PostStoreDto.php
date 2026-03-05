<?php

namespace App\Post\Services\PostStore\DTO;

use App\Post\Http\Requests\PostStoreRequest;
use Spatie\LaravelData\Data;

class PostStoreDto extends Data
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $content,
    ) {}

    public static function fromRequest(PostStoreRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        $title = $request->validated('title');
        $content = $request->validated('content');
        assert(is_string($title) && is_string($content));

        return new self(
            userId: $user->id,
            title: $title,
            content: $content,
        );
    }
}
