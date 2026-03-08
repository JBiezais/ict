<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostUpdateRequest;
use Tests\TestCase;

class PostUpdateRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new PostUpdateRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new PostUpdateRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:255'], $rules['title']);
        $this->assertEquals(['required', 'string'], $rules['content']);
        $this->assertEquals(['nullable', 'array'], $rules['category_ids']);
        $this->assertEquals(['integer', 'exists:categories,id'], $rules['category_ids.*']);
    }
}
