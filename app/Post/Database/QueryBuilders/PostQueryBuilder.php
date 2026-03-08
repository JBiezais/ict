<?php

namespace App\Post\Database\QueryBuilders;

use App\Category\Database\Models\Category;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @extends Builder<\App\Post\Database\Models\Post>
 */
class PostQueryBuilder extends Builder
{
    public function __construct($query, protected PostTsQueryBuilder $tsQueryBuilder)
    {
        parent::__construct($query);
    }

    public function filterByUser(?int $userId): self
    {
        if ($userId !== null) {
            $this->where('user_id', $userId);
        }

        return $this;
    }

    public function loadForIndex(bool $loadUser = false): self
    {
        $this->with($loadUser ? ['categories', 'user'] : ['categories'])
            ->withCount('comments');

        return $this;
    }

    /**
     * @param  list<int>  $categoryIds
     */
    public function filterByCategories(array $categoryIds, bool $includeUncategorized = true, bool $onlyUncategorizedExplicit = false): self
    {
        $allCategoryIds = Category::pluck('id')->all();
        if ($onlyUncategorizedExplicit) {
            $this->whereDoesntHave('categories');

            return $this;
        }
        $applyCategoryFilter = ! empty($categoryIds) && count($categoryIds) < count($allCategoryIds);
        if ($applyCategoryFilter) {
            $this->where(function ($q) use ($categoryIds, $includeUncategorized): void {
                $q->whereHas('categories', fn ($sub) => $sub->whereIn('id', $categoryIds));
                if ($includeUncategorized) {
                    $q->orWhereDoesntHave('categories');
                }
            });
        } elseif (! $includeUncategorized) {
            $this->whereHas('categories');
        }

        return $this;
    }

    public function filterByDateRange(?string $dateFrom, ?string $dateTo): self
    {
        if ($dateFrom !== null) {
            $this->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== null) {
            $this->whereDate('created_at', '<=', $dateTo);
        }

        return $this;
    }

    public function searchByFullText(?string $search): self
    {
        $tsQuery = $search !== null && $search !== ''
            ? $this->tsQueryBuilder->build($search)
            : null;

        $useFullTextSearch = $tsQuery !== null
            && DB::connection()->getDriverName() === 'pgsql'
            && Schema::hasColumn('posts', 'search_vector');

        if ($useFullTextSearch) {
            $this->whereRaw("search_vector @@ to_tsquery('simple', ?)", [$tsQuery])
                ->addSelect(DB::raw('posts.*'))
                ->selectRaw("ts_rank(search_vector, to_tsquery('simple', ?)) as search_rank", [$tsQuery])
                ->orderByDesc('search_rank');
        }

        return $this;
    }

    public function orderBySort(string $sort = 'date'): self
    {
        match ($sort) {
            'date_asc' => $this->orderBy('created_at', 'asc'),
            'comments' => $this->orderByDesc('comments_count')->orderByDesc('created_at'),
            'comments_asc' => $this->orderBy('comments_count')->orderBy('created_at'),
            default => $this->latest(),
        };

        return $this;
    }
}
