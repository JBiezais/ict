<?php

namespace Tests\Feature\Auth\Http\Controllers;

use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notification_can_be_sent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
    }

    public function test_user_is_redirected_when_already_verified(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect(route('my-posts.posts.index', absolute: false));
    }
}
