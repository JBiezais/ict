<?php

namespace App\Post\Services\PostStore\DTO;

use App\Category\Database\Models\Category;
use App\Post\Http\Requests\PostStoreRequest;
use InvalidArgumentException;
use RuntimeException;
use Spatie\LaravelData\Data;

class PostStoreDto extends Data
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $content,
        /** @var list<int> */
        public readonly array $categoryIds,
    ) {}

    public static function fromRequest(PostStoreRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new RuntimeException('Authenticated user is required.');
        }

        $title = $request->validated('title');
        $content = $request->validated('content');
        if (! is_string($title) || ! is_string($content)) {
            throw new InvalidArgumentException('Title and content must be strings.');
        }

        $categoryUuids = $request->validated('category_uuids') ?? [];
        $categoryUuids = is_array($categoryUuids) ? array_filter(array_map('strval', $categoryUuids)) : [];
        $categoryIds = empty($categoryUuids)
            ? []
            : Category::whereIn('uuid', $categoryUuids)->pluck('id')->all();

        return new self(
            userId: $user->id,
            title: $title,
            content: $content,
            categoryIds: array_values($categoryIds),
        );
    }
}
