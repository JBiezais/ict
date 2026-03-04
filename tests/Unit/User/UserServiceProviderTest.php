<?php

namespace Tests\Unit\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('profile.edit'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('profile.update'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('profile.destroy'));
    }
}
