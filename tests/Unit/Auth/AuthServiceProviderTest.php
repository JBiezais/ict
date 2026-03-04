<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('login'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('register'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('password.request'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('logout'));
    }
}
