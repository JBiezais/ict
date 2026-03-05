<?php

namespace Tests\Unit\Comment\Services\CommentStore;

use App\Comment\Database\Models\Comment;
use App\Comment\Services\CommentStore\CommentStoreService;
use App\Comment\Services\CommentStore\DTO\CommentStoreDto;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentStoreServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_comment_with_correct_attributes(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $dto = new CommentStoreDto(
            postId: $post->id,
            userId: $user->id,
            parentId: null,
            content: 'Test comment content.',
        );

        $service = new CommentStoreService;
        $comment = $service->execute($dto);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'content' => 'Test comment content.',
        ]);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals('Test comment content.', $comment->content);
    }

    public function test_execute_creates_reply_with_parent_id(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $parent = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);
        $dto = new CommentStoreDto(
            postId: $post->id,
            userId: $user->id,
            parentId: $parent->id,
            content: 'Reply content.',
        );

        $service = new CommentStoreService;
        $comment = $service->execute($dto);

        $this->assertEquals($parent->id, $comment->parent_id);
        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'content' => 'Reply content.',
        ]);
    }
}
