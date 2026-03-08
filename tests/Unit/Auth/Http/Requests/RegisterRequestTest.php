<?php

namespace Tests\Unit\Auth\Http\Requests;

use App\Auth\Http\Requests\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new RegisterRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new RegisterRequest;

        $rules = $request->rules();

        $this->assertContains('required', $rules['name']);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('required', $rules['password']);
        $this->assertContains('confirmed', $rules['password']);
    }

    public function test_email_has_unique_rule(): void
    {
        $request = new RegisterRequest;

        $rules = $request->rules();

        $emailRules = $rules['email'];
        $this->assertIsArray($emailRules);
        $hasUnique = collect($emailRules)->contains(fn ($rule) => is_string($rule) && str_starts_with($rule, 'unique:'));
        $this->assertTrue($hasUnique, 'Email rules should contain unique validation');
    }
}
