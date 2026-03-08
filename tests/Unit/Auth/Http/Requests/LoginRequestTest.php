<?php

namespace Tests\Unit\Auth\Http\Requests;

use App\Auth\Http\Requests\LoginRequest;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new LoginRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new LoginRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'email'], $rules['email']);
        $this->assertEquals(['required', 'string'], $rules['password']);
    }
}
