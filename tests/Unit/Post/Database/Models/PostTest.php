<?php

namespace Tests\Unit\Post\Database\Models;

use App\Post\Database\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $post = new Post;

        $this->assertEquals(['user_id', 'title', 'content'], $post->getFillable());
    }

    public function test_user_relationship_returns_belongs_to(): void
    {
        $post = new Post;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $post->user());
    }

    public function test_new_factory_returns_post_factory(): void
    {
        $factory = Post::newFactory();

        $this->assertInstanceOf(\Database\Factories\PostFactory::class, $factory);
    }
}
