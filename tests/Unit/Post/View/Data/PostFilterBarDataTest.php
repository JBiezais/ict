<?php

namespace Tests\Unit\Post\View\Data;

use App\Category\Database\Models\Category;
use App\Post\View\Data\PostFilterBarData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PostFilterBarDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_filters_from_request_returns_defaults_when_no_params(): void
    {
        $request = Request::create('/');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame([], $filters['category_ids']);
        $this->assertTrue($filters['include_uncategorized']);
        $this->assertNull($filters['date_from']);
        $this->assertNull($filters['date_to']);
        $this->assertSame('date', $filters['sort']);
        $this->assertNull($filters['search']);
    }

    public function test_current_filters_from_request_parses_category_ids(): void
    {
        $request = Request::create('/?'.http_build_query([
            'category_ids' => [1, 2, '3', 'abc', 0],
        ]));

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame([1, 2, 3], $filters['category_ids']);
    }

    public function test_current_filters_from_request_handles_non_array_category_ids(): void
    {
        $request = Request::create('/?category_ids=1');
        $request->query->set('category_ids', 'scalar');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame([], $filters['category_ids']);
    }

    public function test_current_filters_from_request_parse_include_uncategorized_false(): void
    {
        $request = Request::create('/?include_uncategorized=0');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertFalse($filters['include_uncategorized']);
    }

    public function test_current_filters_from_request_parse_include_uncategorized_string_false(): void
    {
        $request = Request::create('/?include_uncategorized=false');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertFalse($filters['include_uncategorized']);
    }

    public function test_current_filters_from_request_parses_dates(): void
    {
        $request = Request::create('/?date_from=2024-01-15&date_to=2024-02-20');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame('2024-01-15', $filters['date_from']);
        $this->assertSame('2024-02-20', $filters['date_to']);
    }

    public function test_current_filters_from_request_returns_null_for_non_string_dates(): void
    {
        $request = Request::create('/');
        $request->query->set('date_from', 123);
        $request->query->set('date_to', ['not' => 'string']);

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertNull($filters['date_from']);
        $this->assertNull($filters['date_to']);
    }

    public function test_current_filters_from_request_parses_sort_override(): void
    {
        $request = Request::create('/?sort=comments');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame('comments', $filters['sort']);
    }

    public function test_current_filters_from_request_falls_back_to_date_for_non_string_sort(): void
    {
        $request = Request::create('/');
        $request->query->set('sort', 123);

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame('date', $filters['sort']);
    }

    public function test_current_filters_from_request_parses_search_and_collapses_whitespace(): void
    {
        $request = Request::create('/?'.http_build_query(['search' => '  foo   bar   baz  ']));

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertSame('foo bar baz', $filters['search']);
    }

    public function test_current_filters_from_request_returns_null_for_short_search(): void
    {
        $request = Request::create('/?search=a');

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertNull($filters['search']);
    }

    public function test_current_filters_from_request_returns_null_for_non_string_search(): void
    {
        $request = Request::create('/');
        $request->query->set('search', 123);

        $filters = PostFilterBarData::currentFiltersFromRequest($request);

        $this->assertNull($filters['search']);
    }

    public function test_from_filters_and_categories_with_empty_filters_uses_all_categories(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $categories = Category::orderBy('name')->get();

        $data = PostFilterBarData::fromFiltersAndCategories([], $categories);

        $this->assertEqualsCanonicalizing([$tech->id, $php->id], $data->selectedCategoryIds);
        $this->assertTrue($data->includeUncategorized);
        $this->assertFalse($data->hasActiveFilters);
        $this->assertFalse($data->hasActiveSort);
    }

    public function test_from_filters_and_categories_with_category_ids_uses_selected(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $laravel = Category::factory()->create(['name' => 'Laravel']);
        $categories = Category::orderBy('name')->get();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'category_ids' => [$tech->id, $php->id],
        ], $categories);

        $this->assertEqualsCanonicalizing([$tech->id, $php->id], $data->selectedCategoryIds);
        $this->assertTrue($data->hasActiveFilters);
    }

    public function test_from_filters_and_categories_with_include_uncategorized_false_sets_has_active_filters(): void
    {
        $categories = Category::factory()->count(2)->create()->sortBy('name')->values();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'include_uncategorized' => false,
        ], $categories);

        $this->assertFalse($data->includeUncategorized);
        $this->assertTrue($data->hasActiveFilters);
    }

    public function test_from_filters_and_categories_with_date_range_sets_has_active_filters(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
        ], $categories);

        $this->assertSame('2024-01-01', $data->dateFrom);
        $this->assertSame('2024-01-31', $data->dateTo);
        $this->assertTrue($data->hasActiveFilters);
        $this->assertSame('01/01/2024 - 31/01/2024', $data->dateRangeValue);
    }

    public function test_from_filters_and_categories_with_search_sets_has_active_filters(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'search' => 'laravel',
        ], $categories);

        $this->assertSame('laravel', $data->search);
        $this->assertTrue($data->hasActiveFilters);
    }

    public function test_from_filters_and_categories_with_non_date_sort_sets_has_active_sort(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'sort' => 'comments',
        ], $categories);

        $this->assertSame('comments', $data->sort);
        $this->assertTrue($data->hasActiveSort);
    }

    public function test_from_filters_and_categories_format_date_range_value_single_date(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'date_from' => '2024-06-15',
        ], $categories);

        $this->assertSame('15/06/2024', $data->dateRangeValue);
    }

    public function test_from_filters_and_categories_format_date_range_value_invalid_dates_fallback(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'date_from' => 'invalid',
            'date_to' => 'also-bad',
        ], $categories);

        $this->assertSame('invalid - also-bad', $data->dateRangeValue);
    }

    public function test_from_filters_and_categories_format_date_range_value_single_invalid_date(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([
            'date_from' => 'not-a-date',
        ], $categories);

        $this->assertSame('not-a-date', $data->dateRangeValue);
    }

    public function test_from_filters_and_categories_format_date_range_value_empty(): void
    {
        $categories = collect();

        $data = PostFilterBarData::fromFiltersAndCategories([], $categories);

        $this->assertSame('', $data->dateRangeValue);
    }
}
