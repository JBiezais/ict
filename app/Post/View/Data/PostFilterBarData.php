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
        /** @var list<string> */
        public readonly array $selectedCategoryUuids,
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
     * @return array{category_uuids: list<string>, include_uncategorized: bool, date_from: string|null, date_to: string|null, sort: string, search: string|null}
     */
    public static function currentFiltersFromRequest(Request $request): array
    {
        $categoryUuids = $request->input('category_uuids', []);
        $categoryUuids = is_array($categoryUuids) ? $categoryUuids : [];

        return [
            'category_uuids' => self::parseCategoryUuids($categoryUuids),
            'include_uncategorized' => self::parseIncludeUncategorized($request->input('include_uncategorized', true)),
            'date_from' => self::parseDate($request->input('date_from')),
            'date_to' => self::parseDate($request->input('date_to')),
            'sort' => self::parseSort($request->input('sort', 'date')),
            'search' => self::parseSearch($request->input('search')),
            'filter_applied' => $request->has('filter_applied'),
        ];
    }

    /**
     * @param  array<mixed>  $values
     * @return list<string>
     */
    private static function parseCategoryUuids(array $values): array
    {
        $result = [];
        foreach ($values as $v) {
            $uuid = is_string($v) ? trim($v) : '';
            if ($uuid !== '') {
                $result[] = $uuid;
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
     * @param  array{category_uuids?: array<string>, include_uncategorized?: bool|string, date_from?: string|null, date_to?: string|null, sort?: string, search?: string|null, filter_applied?: bool}  $currentFilters
     * @param  Collection<int, Category>  $categories
     */
    public static function fromFiltersAndCategories(array $currentFilters, Collection $categories): self
    {
        $categoryUuidsRaw = (array) ($currentFilters['category_uuids'] ?? []);
        /** @var list<string> $categoryUuids */
        $categoryUuids = collect($categoryUuidsRaw)->map(fn (mixed $v): string => (string) $v)->filter()->values()->all();
        /** @var list<string> $allCategoryUuids */
        $allCategoryUuids = $categories->pluck('uuid')->all();
        $includeUncategorized = (bool) filter_var($currentFilters['include_uncategorized'] ?? '1', FILTER_VALIDATE_BOOLEAN);
        $filterApplied = ! empty($currentFilters['filter_applied']);
        // Only auto-select all categories when user explicitly unchecked everything including Uncategorized (reset to default)
        $nothingSelected = $filterApplied && empty($categoryUuids) && ! $includeUncategorized;
        $onlyUncategorizedChosen = $filterApplied && empty($categoryUuids) && $includeUncategorized;
        $selectedCategoryUuids = $nothingSelected
            ? $allCategoryUuids
            : ($onlyUncategorizedChosen ? [] : (empty($categoryUuids) ? $allCategoryUuids : $categoryUuids));
        if ($nothingSelected) {
            $includeUncategorized = true;
        }
        $dateFrom = (string) ($currentFilters['date_from'] ?? '');
        $dateTo = (string) ($currentFilters['date_to'] ?? '');
        $sort = (string) ($currentFilters['sort'] ?? 'date');
        $search = (string) ($currentFilters['search'] ?? '');

        $dateRangeValue = self::formatDateRangeValue($dateFrom, $dateTo);

        $hasActiveFilters =
            count($selectedCategoryUuids) < count($allCategoryUuids)
            || ! $includeUncategorized
            || $dateFrom !== ''
            || $dateTo !== ''
            || $search !== '';
        $hasActiveSort = $sort !== 'date';

        return new self(
            selectedCategoryUuids: $selectedCategoryUuids,
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
