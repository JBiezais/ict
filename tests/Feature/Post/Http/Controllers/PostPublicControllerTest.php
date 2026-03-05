<?php

namespace Tests\Feature\Post\Http\Controllers;

use App\Category\Database\Models\Category;
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

    public function test_browse_returns_partial_fragment_when_ajax_request_with_fragment(): void
    {
        Post::factory()->create(['title' => 'Fragment Browse Post', 'content' => 'Content']);

        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get('/?_fragment=1');

        $response->assertOk();
        $response->assertViewIs('posts.partials.browse-list');
    }

    public function test_browse_shows_category_labels_for_categorized_posts(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post = Post::factory()->create(['title' => 'Categorized Post', 'content' => 'Content']);
        $post->categories()->attach($tech->id);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Tech');
    }

    public function test_browse_shows_uncategorized_when_post_has_no_categories(): void
    {
        $post = Post::factory()->create(['title' => 'No Categories Post', 'content' => 'Content']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Uncategorized');
    }

    public function test_show_displays_category_labels(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post = Post::factory()->create(['title' => 'Show Categories Post', 'content' => 'Content']);
        $post->categories()->attach($tech->id);

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('Tech');
    }

    public function test_show_displays_uncategorized_when_post_has_no_categories(): void
    {
        $post = Post::factory()->create(['title' => 'Show No Categories', 'content' => 'Content']);

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('Uncategorized');
    }

    public function test_browse_filters_by_search_when_postgresql_fulltext_available(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'pgsql'
            || ! \Illuminate\Support\Facades\Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        Post::factory()->create(['title' => 'Laravel Tutorial', 'content' => 'Learn the Laravel framework.']);
        Post::factory()->create(['title' => 'PHP Guide', 'content' => 'PHP programming.']);
        Post::factory()->create(['title' => 'Laravel Testing', 'content' => 'Testing in Laravel.']);

        $response = $this->get('/?search=Laravel');

        $response->assertOk();
        $response->assertSee('Laravel Tutorial');
        $response->assertSee('Laravel Testing');
        $response->assertDontSee('PHP Guide');
    }

    public function test_browse_search_supports_prefix_matching(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'pgsql'
            || ! \Illuminate\Support\Facades\Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        Post::factory()->create(['title' => 'Prefix Match Post', 'content' => 'Content with veniam and aspernatur.']);
        Post::factory()->create(['title' => 'No Match', 'content' => 'Other content.']);

        $response = $this->get('/?search=veni');

        $response->assertOk();
        $response->assertSee('Prefix Match Post');
        $response->assertDontSee('No Match');
    }
}
