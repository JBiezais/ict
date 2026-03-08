<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new PostStoreRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new PostStoreRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:255'], $rules['title']);
        $this->assertEquals(['required', 'string'], $rules['content']);
        $this->assertEquals(['nullable', 'array'], $rules['category_uuids']);
        $this->assertEquals(['uuid', 'exists:categories,uuid'], $rules['category_uuids.*']);
    }

    public function test_category_uuids_validation_accepts_array_of_uuids(): void
    {
        $tech = \App\Category\Database\Models\Category::factory()->create(['name' => 'Tech']);
        $laravel = \App\Category\Database\Models\Category::factory()->create(['name' => 'Laravel']);

        $request = PostStoreRequest::create('/posts', 'POST', [
            'title' => 'Title',
            'content' => 'Content',
            'category_uuids' => [$tech->uuid, $laravel->uuid],
        ]);
        $request->setContainer(app());

        $request->validateResolved();

        $this->assertEqualsCanonicalizing([$tech->uuid, $laravel->uuid], $request->validated('category_uuids'));
    }
}
