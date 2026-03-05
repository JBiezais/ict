<?php

namespace App\Post\View\Data;

use App\Category\Database\Models\Category;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PostFilterBarData extends Data
{
    public function __construct(
        /** @var list<int> */
        public readonly array $selectedCategoryIds,
        public readonly bool $includeUncategorized,
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly string $sort,
        public readonly string $search,
        public readonly string $dateRangeValue,
        public readonly bool $hasActiveFilters,
        public readonly bool $hasActiveSort,
    ) {}

    /**
     * Build currentFilters array from request, using same parsing semantics as PostIndexDto.
     *
     * @return array{category_ids: list<int>, include_uncategorized: bool, date_from: string|null, date_to: string|null, sort: string, search: string|null}
     */
    public static function currentFiltersFromRequest(Request $request): array
    {
        $categoryIds = $request->input('category_ids', []);
        $categoryIds = is_array($categoryIds) ? $categoryIds : [];

        return [
            'category_ids' => self::parseCategoryIds($categoryIds),
            'include_uncategorized' => self::parseIncludeUncategorized($request->input('include_uncategorized', true)),
            'date_from' => self::parseDate($request->input('date_from')),
            'date_to' => self::parseDate($request->input('date_to')),
            'sort' => self::parseSort($request->input('sort', 'date')),
            'search' => self::parseSearch($request->input('search')),
        ];
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

    /**
     * @param  array{category_ids?: array<int|string>, include_uncategorized?: bool|string, date_from?: string|null, date_to?: string|null, sort?: string, search?: string|null}  $currentFilters
     * @param  Collection<int, Category>  $categories
     */
    public static function fromFiltersAndCategories(array $currentFilters, Collection $categories): self
    {
        $categoryIdsRaw = (array) ($currentFilters['category_ids'] ?? []);
        /** @var list<int> $categoryIds */
        $categoryIds = collect($categoryIdsRaw)->map(fn (mixed $v): int => (int) $v)->filter()->values()->all();
        /** @var list<int> $allCategoryIds */
        $allCategoryIds = $categories->pluck('id')->map(fn (mixed $id): int => is_int($id) ? $id : (int) (is_numeric($id) ? $id : 0))->all();
        $selectedCategoryIds = empty($categoryIds) ? $allCategoryIds : $categoryIds;
        $includeUncategorized = (bool) filter_var($currentFilters['include_uncategorized'] ?? '1', FILTER_VALIDATE_BOOLEAN);
        $dateFrom = (string) ($currentFilters['date_from'] ?? '');
        $dateTo = (string) ($currentFilters['date_to'] ?? '');
        $sort = (string) ($currentFilters['sort'] ?? 'date');
        $search = (string) ($currentFilters['search'] ?? '');

        $dateRangeValue = self::formatDateRangeValue($dateFrom, $dateTo);

        $hasActiveFilters =
            count($selectedCategoryIds) < count($allCategoryIds)
            || ! $includeUncategorized
            || $dateFrom !== ''
            || $dateTo !== ''
            || $search !== '';
        $hasActiveSort = $sort !== 'date';

        return new self(
            selectedCategoryIds: $selectedCategoryIds,
            includeUncategorized: $includeUncategorized,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            sort: $sort,
            search: $search,
            dateRangeValue: $dateRangeValue,
            hasActiveFilters: $hasActiveFilters,
            hasActiveSort: $hasActiveSort,
        );
    }

    private static function formatDateRangeValue(string $dateFrom, string $dateTo): string
    {
        if ($dateFrom && $dateTo) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $dateFrom);
                $to = Carbon::createFromFormat('Y-m-d', $dateTo);
                // @codeCoverageIgnoreStart - defensive null check, Carbon throws on invalid format
                if ($from === null || $to === null) {
                    return "{$dateFrom} - {$dateTo}"; // @codeCoverageIgnore
                }
                // @codeCoverageIgnoreEnd

                return $from->format('d/m/Y').' - '.$to->format('d/m/Y');
            } catch (InvalidFormatException) {
                return "{$dateFrom} - {$dateTo}";
            }
        }

        if ($dateFrom) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $dateFrom);
                // @codeCoverageIgnoreStart - defensive null check, Carbon throws on invalid format
                if ($from === null) {
                    return $dateFrom;
                }
                // @codeCoverageIgnoreEnd

                return $from->format('d/m/Y');
            } catch (InvalidFormatException) {
                return $dateFrom;
            }
        }

        return '';
    }
}
