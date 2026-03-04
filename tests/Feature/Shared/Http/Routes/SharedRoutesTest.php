<?php

namespace Tests\Feature\Shared\Http\Routes;

use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_dashboard_redirects_guest_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_redirects_authenticated_verified_user_to_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/');
    }
}
