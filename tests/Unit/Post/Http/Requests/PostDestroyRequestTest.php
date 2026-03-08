<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Http\Requests\PostDestroyRequest;
use Tests\TestCase;

class PostDestroyRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new PostDestroyRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_empty_array(): void
    {
        $request = new PostDestroyRequest;

        $this->assertSame([], $request->rules());
    }
}
