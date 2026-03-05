<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostBrowseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use ReflectionClass;
use Tests\TestCase;

class PostBrowseRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new PostBrowseRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new PostBrowseRequest;

        $rules = $request->rules();

        $this->assertEquals(['sometimes', 'integer', 'min:1'], $rules['page']);
        $this->assertEquals(['sometimes', 'array'], $rules['category_ids']);
        $this->assertEquals(['nullable', 'date'], $rules['date_from']);
        $this->assertEquals(['nullable', 'date', 'after_or_equal:date_from'], $rules['date_to']);
        $this->assertEquals(['sometimes', 'string', 'in:date,date_asc,comments,comments_asc'], $rules['sort']);
        $this->assertEquals(['sometimes', 'boolean'], $rules['include_uncategorized']);
        $this->assertEquals(['sometimes', 'nullable', 'string', 'max:200'], $rules['search']);
    }

    public function test_prepare_for_validation_trims_and_collapses_search_whitespace(): void
    {
        $response = $this->get('/?' . http_build_query(['search' => '  foo   bar  ']));

        $response->assertOk();
        $response->assertViewHas('currentFilters');
        $filters = $response->viewData('currentFilters');
        $this->assertEquals('foo bar', $filters['search']);
    }

    public function test_prepare_for_validation_converts_empty_date_from_to_null(): void
    {
        $request = PostBrowseRequest::createFrom(Request::create('/?date_from=', 'GET'))
            ->setContainer($this->app);
        $request->query->set('date_from', '');

        $this->invokePrepareForValidation($request);

        $this->assertNull($request->input('date_from'));
    }

    public function test_prepare_for_validation_converts_empty_date_to_to_null(): void
    {
        $request = PostBrowseRequest::createFrom(Request::create('/?date_to=', 'GET'))
            ->setContainer($this->app);
        $request->query->set('date_to', '');

        $this->invokePrepareForValidation($request);

        $this->assertNull($request->input('date_to'));
    }

    private function invokePrepareForValidation(PostBrowseRequest $request): void
    {
        $reflection = new ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
    }
}
