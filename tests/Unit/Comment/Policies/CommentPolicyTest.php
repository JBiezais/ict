<?php

namespace Tests\Unit\Comment\Policies;

use App\Comment\Database\Models\Comment;
use App\Comment\Policies\CommentPolicy;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_allows_owner(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $policy = new CommentPolicy;

        $this->assertTrue($policy->update($user, $comment));
    }

    public function test_update_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);
        $policy = new CommentPolicy;

        $this->assertFalse($policy->update($otherUser, $comment));
    }

    public function test_delete_allows_owner(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $policy = new CommentPolicy;

        $this->assertTrue($policy->delete($user, $comment));
    }

    public function test_delete_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);
        $policy = new CommentPolicy;

        $this->assertFalse($policy->delete($otherUser, $comment));
    }
}
