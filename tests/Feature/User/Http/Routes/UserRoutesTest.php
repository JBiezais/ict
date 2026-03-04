<?php

namespace Tests\Feature\User\Http\Routes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_routes_are_registered(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('profile.edit'));
        $this->assertNotNull(Route::getRoutes()->getByName('profile.update'));
        $this->assertNotNull(Route::getRoutes()->getByName('profile.destroy'));
    }
}
