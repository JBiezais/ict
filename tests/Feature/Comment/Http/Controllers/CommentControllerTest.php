<?php

namespace Tests\Feature\Comment\Http\Controllers;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_comment_and_redirects(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'content' => 'My new comment content.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', __('Comment added.'));
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'My new comment content.',
        ]);
    }

    public function test_store_fails_validation(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_store_with_parent_id_creates_reply(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $parent = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'content' => 'Reply to parent comment.',
            'parent_id' => (string) $parent->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'content' => 'Reply to parent comment.',
        ]);
    }

    public function test_store_parent_id_validation_fails_when_parent_on_different_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $otherPost = Post::factory()->create();
        $commentOnOtherPost = Comment::factory()->create(['post_id' => $otherPost->id]);

        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'content' => 'Invalid reply.',
            'parent_id' => (string) $commentOnOtherPost->id,
        ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    public function test_update_succeeds_for_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id, 'content' => 'Original']);

        $response = $this->actingAs($user)->put(
            route('posts.comments.update', [$post, $comment]),
            ['content' => 'Updated content']
        );

        $response->assertRedirect();
        $response->assertSessionHas('status', __('Comment updated.'));
        $comment->refresh();
        $this->assertEquals('Updated content', $comment->content);
    }

    public function test_update_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put(
            route('posts.comments.update', [$post, $comment]),
            ['content' => 'Hacked content']
        );

        $response->assertForbidden();
    }

    public function test_update_comment_on_wrong_post_returns_404(): void
    {
        $user = User::factory()->create();
        $postA = Post::factory()->create();
        $postB = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $postB->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->put(
            route('posts.comments.update', [$postA, $comment]),
            ['content' => 'Should 404']
        );

        $response->assertNotFound();
    }

    public function test_destroy_succeeds_for_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.comments.destroy', [$post, $comment]));

        $response->assertRedirect();
        $response->assertSessionHas('status', __('Comment deleted.'));
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_destroy_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->delete(route('posts.comments.destroy', [$post, $comment]));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_destroy_comment_on_wrong_post_returns_404(): void
    {
        $user = User::factory()->create();
        $postA = Post::factory()->create();
        $postB = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $postB->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.comments.destroy', [$postA, $comment]));

        $response->assertNotFound();
    }

    public function test_guest_cannot_store(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('posts.comments.store', $post), [
            'content' => 'Guest comment',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_update(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->put(route('posts.comments.update', [$post, $comment]), [
            'content' => 'Guest update',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_destroy(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->delete(route('posts.comments.destroy', [$post, $comment]));

        $response->assertRedirect('/login');
    }
}
