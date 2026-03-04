<?php

namespace Tests\Feature\Auth\Http\Controllers;

use App\User\Database\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
        $response->assertSessionHasNoErrors();
    }

    public function test_reset_password_link_fails_with_invalid_email(): void
    {
        $response = $this->post('/forgot-password', ['email' => '']);

        $response->assertSessionHasErrors('email');
    }
}
