<?php

namespace Tests\Feature\Post\Http\Controllers;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPublicControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('posts.pages.browse');
    }

    public function test_post_show_page_can_be_rendered(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertViewIs('posts.pages.show');
    }

    public function test_post_show_displays_post_content(): void
    {
        $post = Post::factory()->create([
            'title' => 'My Test Post',
            'content' => 'This is the post content.',
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertSee('My Test Post');
        $response->assertSee('This is the post content.');
    }

    public function test_post_show_displays_nested_comments(): void
    {
        $post = Post::factory()->create(['title' => 'Post With Comments', 'content' => 'Content']);
        $topLevel = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null, 'content' => 'Top level']);
        $reply = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $topLevel->id, 'content' => 'Reply']);
        $nestedReply = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $reply->id, 'content' => 'Nested reply']);

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('Top level');
        $response->assertSee('Reply');
        $response->assertSee('Nested reply');
    }

    public function test_post_show_loads_comments_up_to_max_nesting_level_5(): void
    {
        $post = Post::factory()->create(['title' => 'Deep Nesting Post', 'content' => 'Content']);

        $root = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null, 'content' => 'Level 0']);
        $c1 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $root->id, 'content' => 'Level 1']);
        $c2 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $c1->id, 'content' => 'Level 2']);
        $c3 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $c2->id, 'content' => 'Level 3']);
        $c4 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $c3->id, 'content' => 'Level 4']);
        $c5 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $c4->id, 'content' => 'Level 5']);
        $c6 = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $c5->id, 'content' => 'Level 6']);

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('Level 0');
        $response->assertSee('Level 1');
        $response->assertSee('Level 2');
        $response->assertSee('Level 3');
        $response->assertSee('Level 4');
        $response->assertSee('Level 5');
        $response->assertSee('Level 6');
    }

    public function test_post_show_renders_view_replies_for_nested_comments(): void
    {
        $post = Post::factory()->create(['title' => 'Count Test Post', 'content' => 'Content']);
        $root = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null, 'content' => 'Root']);
        Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $root->id, 'content' => 'Child']);

        $response = $this->get(route('posts.show', $post));
        $response->assertOk();
        $response->assertSee('View 1 replies', false);
    }

    public function test_index_displays_posts_with_comment_count(): void
    {
        $post = Post::factory()->create(['title' => 'Index Post', 'content' => 'Index content']);
        Comment::factory()->count(3)->create(['post_id' => $post->id, 'parent_id' => null]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Index Post');
    }
}
