<?php

namespace Tests\Unit\Auth\Http\Requests;

use App\Auth\Http\Requests\ConfirmPasswordRequest;
use Tests\TestCase;

class ConfirmPasswordRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new ConfirmPasswordRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new ConfirmPasswordRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string'], $rules['password']);
    }
}
