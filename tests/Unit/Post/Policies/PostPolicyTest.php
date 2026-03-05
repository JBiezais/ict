<?php

namespace Tests\Unit\Post\Policies;

use App\Post\Database\Models\Post;
use App\Post\Policies\PostPolicy;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_allows_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $policy = new PostPolicy;

        $this->assertTrue($policy->update($user, $post));
    }

    public function test_update_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        $policy = new PostPolicy;

        $this->assertFalse($policy->update($otherUser, $post));
    }

    public function test_delete_allows_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $policy = new PostPolicy;

        $this->assertTrue($policy->delete($user, $post));
    }

    public function test_delete_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        $policy = new PostPolicy;

        $this->assertFalse($policy->delete($otherUser, $post));
    }
}
