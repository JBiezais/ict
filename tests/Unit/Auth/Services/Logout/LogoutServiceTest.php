<?php

namespace Tests\Unit\Auth\Services\Logout;

use App\Auth\Services\Logout\LogoutService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tests\TestCase;

class LogoutServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_logs_out_user(): void
    {
        $user = User::factory()->create();

        $request = Request::create('/logout', 'POST');
        $session = Session::driver();
        $session->start();
        $session->put('_token', Str::random(40));
        $request->setLaravelSession($session);
        $this->app->instance('request', $request);
        $this->app->instance('request', $request);

        Auth::guard('web')->login($user);
        $this->assertAuthenticated();

        $service = new LogoutService;
        $service->execute($request);

        $this->assertGuest();
    }
}
