<?php

namespace Tests\Unit\Comment\Http\Requests;

use App\Comment\Http\Requests\CommentDestroyRequest;
use Tests\TestCase;

class CommentDestroyRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new CommentDestroyRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_empty_array(): void
    {
        $request = new CommentDestroyRequest;

        $this->assertEquals([], $request->rules());
    }
}
