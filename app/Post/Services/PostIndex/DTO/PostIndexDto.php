<?php

namespace App\Post\Services\PostIndex\DTO;

use App\Post\Http\Requests\PostBrowseRequest;
use App\Post\Http\Requests\PostIndexRequest;
use Spatie\LaravelData\Data;

class PostIndexDto extends Data
{
    public function __construct(
        /** When null, posts are not filtered by user (e.g. public browse). */
        public readonly ?int $userId = null,
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        /** @var list<int> */
        public readonly array $categoryIds = [],
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly string $sort = 'date',
        public readonly bool $includeUncategorized = true,
        /** When true, eager load user relation (for public browse showing author names). */
        public readonly bool $loadUser = false,
    ) {}

    public static function fromRequest(PostIndexRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        return new self(
            userId: $user->id,
            page: self::parsePage($request->validated('page', 1)),
            perPage: self::parsePerPage($request->validated('per_page', 10)),
            categoryIds: self::parseCategoryIds($request->validated('category_ids', [])),
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: self::parseIncludeUncategorized($request->validated('include_uncategorized', true)),
        );
    }

    public static function fromBrowseRequest(PostBrowseRequest $request): self
    {
        return new self(
            userId: null,
            page: self::parsePage($request->validated('page', 1)),
            perPage: 10,
            categoryIds: self::parseCategoryIds($request->validated('category_ids', [])),
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: self::parseIncludeUncategorized($request->validated('include_uncategorized', true)),
            loadUser: true,
        );
    }

    private static function parsePage(mixed $value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
    }

    private static function parsePerPage(mixed $value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]) ?: 10;
    }

    /** @return list<int> */
    private static function parseCategoryIds(array $values): array
    {
        return collect($values)->map(fn ($v) => (int) $v)->filter()->values()->all();
    }

    private static function parseDate(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    private static function parseSort(mixed $value): string
    {
        return is_string($value) ? $value : 'date';
    }

    private static function parseIncludeUncategorized(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }
}
