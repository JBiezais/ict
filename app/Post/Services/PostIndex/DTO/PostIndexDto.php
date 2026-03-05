<?php

namespace App\Post\Services\PostIndex\DTO;

use App\Post\Http\Requests\PostIndexRequest;
use Spatie\LaravelData\Data;

class PostIndexDto extends Data
{
    public function __construct(
        public readonly int $userId,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(PostIndexRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        $page = filter_var($request->validated('page', 1), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $perPage = filter_var($request->validated('per_page', 10), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]) ?: 10;

        return new self(
            userId: $user->id,
            page: $page,
            perPage: $perPage,
        );
    }
}
