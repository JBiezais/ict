<?php

namespace Tests\Unit\Comment\Http\Requests;

use App\Comment\Http\Requests\CommentUpdateRequest;
use Tests\TestCase;

class CommentUpdateRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new CommentUpdateRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new CommentUpdateRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:2000'], $rules['content']);
    }
}
