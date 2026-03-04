<?php

namespace Tests\Unit\Shared;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('dashboard'));
    }

    public function test_app_layout_component_can_be_rendered(): void
    {
        $user = \App\User\Database\Models\User::factory()->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $this->assertNotEmpty($response->getContent());
    }

    public function test_guest_layout_component_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $this->assertNotEmpty($response->getContent());
    }
}
