<?php

namespace Tests\Feature\Auth\Http\Controllers;

use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationPromptControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered_for_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertOk();
    }

    public function test_user_is_redirected_to_my_posts_when_already_verified(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertRedirect(route('my-posts.posts.index', absolute: false));
    }
}
