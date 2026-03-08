<?php

namespace Tests\Unit\Auth\Services\Login;

use App\Auth\Services\Login\DTO\LoginDto;
use App\Auth\Services\Login\LoginService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_succeeds_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        RateLimiter::clear('test@example.com|127.0.0.1');

        $dto = new LoginDto(
            email: 'test@example.com',
            password: 'password',
            remember: false,
            throttleKey: 'test@example.com|127.0.0.1',
        );
        $request = Request::create('/login', 'POST');
        $this->app->instance('request', $request);

        $service = new LoginService;
        $service->execute($dto, $request);

        $this->assertAuthenticated();
    }

    public function test_execute_fails_with_invalid_credentials(): void
    {
        $this->expectException(ValidationException::class);

        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        RateLimiter::clear('test@example.com|127.0.0.1');

        $dto = new LoginDto(
            email: 'test@example.com',
            password: 'wrong-password',
            remember: false,
            throttleKey: 'test@example.com|127.0.0.1',
        );
        $request = Request::create('/login', 'POST');
        $this->app->instance('request', $request);

        $service = new LoginService;
        $service->execute($dto, $request);
    }

    public function test_execute_throws_when_rate_limited(): void
    {
        $this->expectException(ValidationException::class);

        User::factory()->create(['email' => 'ratelimit@example.com']);

        $dto = new LoginDto(
            email: 'ratelimit@example.com',
            password: 'wrong',
            remember: false,
            throttleKey: 'ratelimit@example.com|192.168.1.100',
        );
        $request = Request::create('/login', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $this->app->instance('request', $request);

        $service = new LoginService;

        for ($i = 0; $i < 5; $i++) {
            try {
                $service->execute($dto, $request);
            } catch (ValidationException) {
                // Expected for failed attempts
            }
        }

        $validDto = new LoginDto(
            email: 'ratelimit@example.com',
            password: 'password',
            remember: false,
            throttleKey: 'ratelimit@example.com|192.168.1.100',
        );
        $service->execute($validDto, $request);
    }

    public function test_execute_clears_rate_limit_on_success(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $dto = new LoginDto(
            email: 'test@example.com',
            password: 'password',
            remember: false,
            throttleKey: 'test@example.com|127.0.0.1',
        );
        $request = Request::create('/login', 'POST');
        $this->app->instance('request', $request);

        $service = new LoginService;
        $service->execute($dto, $request);

        $this->assertEquals(0, RateLimiter::attempts('test@example.com|127.0.0.1'));
    }
}
