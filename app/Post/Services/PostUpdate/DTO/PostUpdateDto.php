<?php

namespace App\Post\Services\PostUpdate\DTO;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\Post\Http\Requests\PostUpdateRequest;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class PostUpdateDto extends Data
{
    public function __construct(
        public readonly int $postId,
        public readonly string $title,
        public readonly string $content,
        /** @var list<int> */
        public readonly array $categoryIds,
    ) {}

    public static function fromRequest(PostUpdateRequest $request, Post $post): self
    {
        $title = $request->validated('title');
        $content = $request->validated('content');
        if (! is_string($title) || ! is_string($content)) {
            throw new InvalidArgumentException('Title and content must be strings.');
        }

        $categoryUuids = $request->validated('category_uuids') ?? [];
        $categoryUuids = is_array($categoryUuids) ? array_filter(array_map(
            /** @phpstan-ignore argument.type */
            fn (mixed $v): string => strval($v),
            $categoryUuids
        )) : [];
        $rawIds = empty($categoryUuids) ? [] : Category::whereIn('uuid', $categoryUuids)->pluck('id')->all();
        $categoryIds = array_map(
            /** @phpstan-ignore argument.type */
            fn (mixed $id): int => is_int($id) ? $id : (int) strval($id),
            $rawIds
        );

        return new self(
            postId: $post->id,
            title: $title,
            content: $content,
            categoryIds: array_values($categoryIds),
        );
    }
}
