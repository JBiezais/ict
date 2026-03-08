<?php

namespace Tests\Unit\Auth\Services\Register;

use App\Auth\Services\Register\DTO\RegisterDto;
use App\Auth\Services\Register\RegisterService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_user_and_logs_in(): void
    {
        Event::fake();

        $dto = new RegisterDto(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
        );

        $service = new RegisterService;
        $user = $service->execute($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertAuthenticated();
        Event::assertDispatched(\Illuminate\Auth\Events\Registered::class);
    }
}
