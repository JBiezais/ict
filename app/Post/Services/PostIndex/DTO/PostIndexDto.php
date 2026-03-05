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
        /** Search keywords for full-text search on title and content (PostgreSQL only). */
        public readonly ?string $search = null,
    ) {}

    public static function fromRequest(PostIndexRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        $categoryIds = $request->validated('category_ids', []);

        return new self(
            userId: $user->id,
            page: self::parsePage($request->validated('page', 1)),
            perPage: self::parsePerPage($request->validated('per_page', 10)),
            categoryIds: self::parseCategoryIds(is_array($categoryIds) ? $categoryIds : []),
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: self::parseIncludeUncategorized($request->validated('include_uncategorized', true)),
            search: self::parseSearch($request->validated('search')),
        );
    }

    public static function fromBrowseRequest(PostBrowseRequest $request): self
    {
        $categoryIds = $request->validated('category_ids', []);

        return new self(
            userId: null,
            page: self::parsePage($request->validated('page', 1)),
            perPage: 10,
            categoryIds: self::parseCategoryIds(is_array($categoryIds) ? $categoryIds : []),
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: self::parseIncludeUncategorized($request->validated('include_uncategorized', true)),
            loadUser: true,
            search: self::parseSearch($request->validated('search')),
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

    /**
     * @param  array<mixed>  $values
     * @return list<int>
     */
    private static function parseCategoryIds(array $values): array
    {
        $result = [];
        foreach ($values as $v) {
            $id = is_numeric($v) ? (int) $v : 0;
            if ($id !== 0) {
                $result[] = $id;
            }
        }

        return $result;
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

    private static function parseSearch(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $replaced = preg_replace('/\s+/', ' ', $value);
        $trimmed = is_string($replaced) ? trim($replaced) : '';

        return $trimmed === '' || strlen($trimmed) < 2 ? null : $trimmed;
    }
}
