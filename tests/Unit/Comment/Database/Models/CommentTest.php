<?php

namespace Tests\Unit\Comment\Database\Models;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $comment = new Comment;

        $this->assertEquals(['post_id', 'user_id', 'parent_id', 'content'], $comment->getFillable());
    }

    public function test_post_relationship_returns_belongs_to(): void
    {
        $comment = new Comment;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $comment->post());
    }

    public function test_user_relationship_returns_belongs_to(): void
    {
        $comment = new Comment;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $comment->user());
    }

    public function test_parent_relationship_returns_belongs_to(): void
    {
        $comment = new Comment;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $comment->parent());
    }

    public function test_children_relationship_returns_has_many(): void
    {
        $comment = new Comment;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $comment->children());
    }

    public function test_comment_belongs_to_post(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->assertEquals($post->id, $comment->post->id);
    }

    public function test_comment_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $comment->user->id);
    }

    public function test_comment_has_children(): void
    {
        $parent = Comment::factory()->create();
        Comment::factory()->count(2)->create(['parent_id' => $parent->id]);

        $this->assertCount(2, $parent->children);
    }

    public function test_new_factory_returns_comment_factory(): void
    {
        $factory = Comment::newFactory();

        $this->assertInstanceOf(\Database\Factories\CommentFactory::class, $factory);
    }
}
