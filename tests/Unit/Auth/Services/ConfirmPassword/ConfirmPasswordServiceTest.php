<?php

namespace Tests\Unit\Auth\Services\ConfirmPassword;

use App\Auth\Services\ConfirmPassword\ConfirmPasswordService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConfirmPasswordServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_confirms_valid_password_and_sets_session(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $request = Request::create('/confirm-password', 'POST');
        $request->setLaravelSession(Session::driver());
        $request->session()->start();
        $this->app->instance('request', $request);

        $service = new ConfirmPasswordService;
        $service->execute($user, 'password', $request);

        $this->assertTrue($request->session()->has('auth.password_confirmed_at'));
    }

    public function test_execute_throws_with_invalid_password(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create(['password' => bcrypt('password')]);

        $request = Request::create('/confirm-password', 'POST');
        $this->app->instance('request', $request);

        $service = new ConfirmPasswordService;
        $service->execute($user, 'wrong-password', $request);
    }
}
