<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostIndexRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use ReflectionClass;
use Tests\TestCase;

class PostIndexRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new PostIndexRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new PostIndexRequest;

        $rules = $request->rules();

        $this->assertEquals(['sometimes', 'integer', 'min:1'], $rules['page']);
        $this->assertEquals(['sometimes', 'integer', 'min:1', 'max:100'], $rules['per_page']);
    }

    public function test_prepare_for_validation_trims_and_collapses_search_whitespace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('my-posts.posts.index', ['search' => '  foo   bar  ']));

        $response->assertOk();
        $response->assertViewHas('filterBarData');
        $filterBarData = $response->viewData('filterBarData');
        $this->assertEquals('foo bar', $filterBarData->search);
    }

    public function test_prepare_for_validation_converts_empty_date_from_to_null(): void
    {
        $request = PostIndexRequest::createFrom(Request::create('/?date_from=', 'GET'))
            ->setContainer($this->app);
        $request->query->set('date_from', '');

        $this->invokePrepareForValidation($request);

        $this->assertNull($request->input('date_from'));
    }

    public function test_prepare_for_validation_converts_empty_date_to_to_null(): void
    {
        $request = PostIndexRequest::createFrom(Request::create('/?date_to=', 'GET'))
            ->setContainer($this->app);
        $request->query->set('date_to', '');

        $this->invokePrepareForValidation($request);

        $this->assertNull($request->input('date_to'));
    }

    private function invokePrepareForValidation(PostIndexRequest $request): void
    {
        $reflection = new ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
    }
}
