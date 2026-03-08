<?php

namespace Tests\Unit\Post\Database\QueryBuilders;

use App\Category\Database\Models\Category;
use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PostQueryBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_user_includes_only_posts_for_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'My Post']);
        Post::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Post']);

        $results = Post::query()
            ->filterByUser($user->id)
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('My Post', $results->first()->title);
    }

    public function test_filter_by_user_with_null_returns_all_posts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'My Post']);
        Post::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Post']);

        $results = Post::query()
            ->filterByUser(null)
            ->get();

        $this->assertCount(2, $results);
    }

    public function test_filter_by_categories_filters_by_category_ids(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $user = User::factory()->create();
        $postInTech = Post::factory()->create(['user_id' => $user->id, 'title' => 'Tech Post']);
        $postInTech->categories()->attach($tech->id);
        $postUncategorized = Post::factory()->create(['user_id' => $user->id, 'title' => 'Uncategorized Post']);
        $postInPhp = Post::factory()->create(['user_id' => $user->id, 'title' => 'PHP Post']);
        $postInPhp->categories()->attach($php->id);

        $results = Post::query()
            ->filterByUser($user->id)
            ->filterByCategories([$tech->id], includeUncategorized: true)
            ->get();

        $this->assertCount(2, $results);
        $titles = $results->pluck('title')->all();
        $this->assertContains('Tech Post', $titles);
        $this->assertContains('Uncategorized Post', $titles);
        $this->assertNotContains('PHP Post', $titles);
    }

    public function test_filter_by_categories_with_only_uncategorized_explicit_returns_uncategorized_only(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $user = User::factory()->create();
        $uncategorizedPost = Post::factory()->create(['user_id' => $user->id, 'title' => 'Uncategorized Post']);
        $categorizedPost = Post::factory()->create(['user_id' => $user->id, 'title' => 'Categorized Post']);
        $categorizedPost->categories()->attach($tech->id);

        $results = Post::query()
            ->filterByUser($user->id)
            ->filterByCategories([], includeUncategorized: true, onlyUncategorizedExplicit: true)
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Uncategorized Post', $results->first()->title);
    }

    public function test_filter_by_date_range_applies_date_constraints(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Old Post', 'created_at' => '2023-06-01']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Recent Post', 'created_at' => '2024-06-01']);

        $results = Post::query()
            ->filterByUser($user->id)
            ->filterByDateRange('2024-01-01', null)
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Recent Post', $results->first()->title);
    }

    public function test_order_by_sort_respects_sort_option(): void
    {
        $user = User::factory()->create();
        $old = Post::factory()->create(['user_id' => $user->id, 'title' => 'Oldest', 'created_at' => '2024-01-01']);
        $middle = Post::factory()->create(['user_id' => $user->id, 'title' => 'Middle', 'created_at' => '2024-06-01']);
        $newest = Post::factory()->create(['user_id' => $user->id, 'title' => 'Newest', 'created_at' => '2024-12-01']);

        $results = Post::query()
            ->filterByUser($user->id)
            ->orderBySort('date_asc')
            ->get();

        $this->assertCount(3, $results);
        $this->assertEquals('Oldest', $results[0]->title);
        $this->assertEquals('Middle', $results[1]->title);
        $this->assertEquals('Newest', $results[2]->title);
    }

    public function test_load_for_index_loads_relations(): void
    {
        $user = User::factory()->create();
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Post']);
        $post->categories()->attach($tech->id);

        $results = Post::query()
            ->filterByUser($user->id)
            ->loadForIndex(loadUser: false)
            ->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->relationLoaded('categories'));
        $this->assertFalse($results->first()->relationLoaded('user'));
        $this->assertNotNull($results->first()->comments_count);
    }

    public function test_search_by_full_text_applies_fulltext_when_available(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Laravel Guide', 'content' => 'Learn Laravel.']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'PHP Basics', 'content' => 'PHP tutorial.']);

        $results = Post::query()
            ->filterByUser($user->id)
            ->loadForIndex()
            ->searchByFullText('Laravel')
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laravel Guide', $results->first()->title);
    }

    public function test_order_by_sort_comments_sorts_by_comments_count(): void
    {
        $user = User::factory()->create();
        $fewComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Few Comments']);
        $manyComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Many Comments']);
        Comment::factory()->count(5)->create(['post_id' => $manyComments->id, 'parent_id' => null]);
        Comment::factory()->count(1)->create(['post_id' => $fewComments->id, 'parent_id' => null]);

        $results = Post::query()
            ->filterByUser($user->id)
            ->loadForIndex()
            ->orderBySort('comments')
            ->get();

        $this->assertCount(2, $results);
        $this->assertEquals('Many Comments', $results[0]->title);
        $this->assertEquals('Few Comments', $results[1]->title);
    }
}
