<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostEditRequest;
use Tests\TestCase;

class PostEditRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new PostEditRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_empty_array(): void
    {
        $request = new PostEditRequest;

        $this->assertSame([], $request->rules());
    }
}
