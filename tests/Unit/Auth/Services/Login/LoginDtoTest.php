<?php

namespace Tests\Unit\Auth\Services\Login;

use App\Auth\Http\Requests\LoginRequest;
use App\Auth\Services\Login\DTO\LoginDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginDtoTest extends TestCase
{
    use RefreshDatabase;

    public function test_from_request_builds_dto_with_throttle_key(): void
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'Test@Example.com',
            'password' => 'password',
            'remember' => true,
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.1');
        $request->setContainer(app());
        $request->validateResolved();

        $dto = LoginDto::fromRequest($request);

        $this->assertEquals('Test@Example.com', $dto->email);
        $this->assertEquals('password', $dto->password);
        $this->assertTrue($dto->remember);
        $this->assertStringContainsString('test@example.com', $dto->throttleKey);
        $this->assertStringContainsString('192.168.1.1', $dto->throttleKey);
    }
}
