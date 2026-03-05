<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostIndexRequest;
use Tests\TestCase;

class PostIndexRequestTest extends TestCase
{
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
}
