<?php

namespace Tests\Feature\Auth\Http\Routes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_routes_are_registered(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('login'));
        $this->assertNotNull(Route::getRoutes()->getByName('register'));
        $this->assertNotNull(Route::getRoutes()->getByName('password.confirm'));
        $this->assertNotNull(Route::getRoutes()->getByName('logout'));
    }
}
