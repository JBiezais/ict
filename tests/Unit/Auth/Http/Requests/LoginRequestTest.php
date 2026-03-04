<?php

namespace Tests\Unit\Auth\Http\Requests;

use App\Auth\Http\Requests\LoginRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_throttle_key_includes_email_and_ip(): void
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'Test@Example.com',
            'password' => 'password',
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.1');

        $key = $request->throttleKey();

        $this->assertStringContainsString('test@example.com', $key);
        $this->assertStringContainsString('192.168.1.1', $key);
    }

    public function test_authenticate_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        RateLimiter::clear('test@example.com|127.0.0.1');

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $request->setContainer(app());
        $request->validateResolved();

        $request->authenticate();

        $this->assertAuthenticated();
    }

    public function test_authenticate_fails_with_invalid_credentials(): void
    {
        $this->expectException(ValidationException::class);

        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        RateLimiter::clear('test@example.com|127.0.0.1');

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        $request->setContainer(app());
        $request->validateResolved();

        $request->authenticate();
    }

    public function test_ensure_is_not_rate_limited_throws_when_too_many_attempts(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create(['email' => 'ratelimit@example.com']);

        $createRequest = function () {
            $request = LoginRequest::create('/login', 'POST', [
                'email' => 'ratelimit@example.com',
                'password' => 'wrong',
            ]);
            $request->server->set('REMOTE_ADDR', '192.168.1.100');
            $request->setContainer(app());
            $request->validateResolved();
            return $request;
        };

        // Exhaust rate limit with 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            try {
                $request = $createRequest();
                $request->authenticate();
            } catch (ValidationException) {
                // Expected for failed attempts
            }
        }

        // 6th attempt should hit rate limit
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'ratelimit@example.com',
            'password' => 'password',
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $request->setContainer(app());
        $request->validateResolved();
        $request->authenticate();
    }
}
