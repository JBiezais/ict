<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_routes_are_registered(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('login'));
        $this->assertNotNull(Route::getRoutes()->getByName('register'));
        $this->assertNotNull(Route::getRoutes()->getByName('logout'));
    }
}
