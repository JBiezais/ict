<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostStoreRequest;
use Tests\TestCase;

class PostStoreRequestTest extends TestCase
{
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
    }
}
