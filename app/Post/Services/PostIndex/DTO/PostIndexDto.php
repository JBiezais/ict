<?php

namespace App\Post\Services\PostIndex\DTO;

use App\Category\Database\Models\Category;
use App\Post\Http\Requests\PostBrowseRequest;
use App\Post\Http\Requests\PostIndexRequest;
use Spatie\LaravelData\Data;

class PostIndexDto extends Data
{
    public function __construct(
        public readonly ?int $userId = null,
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        /** @var list<int> */
        public readonly array $categoryIds = [],
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly string $sort = 'date',
        public readonly bool $includeUncategorized = true,
        public readonly bool $loadUser = false,
        public readonly ?string $search = null,
        public readonly bool $onlyUncategorizedExplicit = false,
    ) {}

    public static function fromRequest(PostIndexRequest $request): self
    {
        $user = $request->user();
        if ($user === null) {
            throw new \RuntimeException('Authenticated user is required.');
        }

        $categoryIds = self::resolveCategoryUuidsToIds(is_array($request->validated('category_uuids', [])) ? $request->validated('category_uuids', []) : []);
        $includeUncategorized = self::parseIncludeUncategorized($request->validated('include_uncategorized', true));
        $filterApplied = filter_var($request->validated('filter_applied', false), FILTER_VALIDATE_BOOLEAN);

        return new self(
            userId: $user->id,
            page: self::parsePage($request->validated('page', 1)),
            perPage: self::parsePerPage($request->validated('per_page', 10)),
            categoryIds: $categoryIds,
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: $includeUncategorized,
            search: self::parseSearch($request->validated('search')),
            onlyUncategorizedExplicit: $filterApplied && $categoryIds === [] && $includeUncategorized,
        );
    }

    public static function fromBrowseRequest(PostBrowseRequest $request): self
    {
        $categoryIds = self::resolveCategoryUuidsToIds(is_array($request->validated('category_uuids', [])) ? $request->validated('category_uuids', []) : []);
        $includeUncategorized = self::parseIncludeUncategorized($request->validated('include_uncategorized', true));
        $filterApplied = filter_var($request->validated('filter_applied', false), FILTER_VALIDATE_BOOLEAN);

        return new self(
            userId: null,
            page: self::parsePage($request->validated('page', 1)),
            perPage: 10,
            categoryIds: $categoryIds,
            dateFrom: self::parseDate($request->validated('date_from')),
            dateTo: self::parseDate($request->validated('date_to')),
            sort: self::parseSort($request->validated('sort', 'date')),
            includeUncategorized: $includeUncategorized,
            loadUser: true,
            search: self::parseSearch($request->validated('search')),
            onlyUncategorizedExplicit: $filterApplied && $categoryIds === [] && $includeUncategorized,
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
    /**
     * @param  array<mixed>  $values
     * @return list<int>
     */
    private static function resolveCategoryUuidsToIds(array $values): array
    {
        $uuids = array_values(array_filter(array_map(
            /** @phpstan-ignore argument.type */
            fn (mixed $v): string => strval($v),
            $values
        )));
        if (empty($uuids)) {
            return [];
        }

        $ids = Category::whereIn('uuid', $uuids)->pluck('id')->all();

        return array_values(array_map(
            /** @phpstan-ignore argument.type */
            fn (mixed $id): int => is_int($id) ? $id : (int) strval($id),
            $ids
        ));
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
