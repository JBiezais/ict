<?php

namespace Tests\Unit\Post\Services\PostIndex;

use App\Category\Database\Models\Category;
use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\PostIndexService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PostIndexServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_paginated_posts_for_user(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);
        Post::factory()->count(2)->create(); // Other users' posts

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10);
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(3, $result->items);
        $this->assertEquals(3, $result->total);
        $this->assertEquals(10, $result->perPage);
        $this->assertEquals(1, $result->currentPage);
    }

    public function test_execute_filters_by_user_id(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'My Post']);
        Post::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Post']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10);
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('My Post', $result->items[0]->title);
    }

    public function test_execute_respects_pagination(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(5)->create(['user_id' => $user->id]);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 2);
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(2, $result->items);
        $this->assertEquals(5, $result->total);
        $this->assertEquals(2, $result->perPage);
        $this->assertEquals(3, $result->lastPage);
    }

    public function test_execute_filters_by_search_when_postgresql_fulltext_available(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Laravel Framework Guide', 'content' => 'Learn Laravel.']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'PHP Basics', 'content' => 'PHP tutorial.']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Laravel Eloquent', 'content' => 'Eloquent ORM.']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, search: 'Laravel');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(2, $result->items);
        $this->assertEquals(2, $result->total);
        $titles = array_map(fn ($item) => $item->title, $result->items);
        $this->assertContains('Laravel Framework Guide', $titles);
        $this->assertContains('Laravel Eloquent', $titles);
        $this->assertNotContains('PHP Basics', $titles);
    }

    public function test_execute_search_supports_prefix_matching(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Lorem', 'content' => 'Veniam aspernatur ipsum.']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Ipsum', 'content' => 'Other content.']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, search: 'veni');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertStringContainsString('veniam', strtolower($result->items[0]->content));
    }

    public function test_execute_search_sanitizes_tsquery_operators(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            $this->markTestSkipped('PostgreSQL full-text search requires search_vector column.');
        }

        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Foo Bar Post', 'content' => 'Contains foo and bar.']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, search: 'foo & bar');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Foo Bar Post', $result->items[0]->title);
    }

    public function test_execute_filters_by_category_ids_with_include_uncategorized(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $user = User::factory()->create();
        $postInTech = Post::factory()->create(['user_id' => $user->id, 'title' => 'Tech Post']);
        $postInTech->categories()->attach($tech->id);
        $postUncategorized = Post::factory()->create(['user_id' => $user->id, 'title' => 'Uncategorized Post']);
        $postInPhp = Post::factory()->create(['user_id' => $user->id, 'title' => 'PHP Post']);
        $postInPhp->categories()->attach($php->id);

        $dto = new PostIndexDto(
            userId: $user->id,
            page: 1,
            perPage: 10,
            categoryIds: [$tech->id],
            includeUncategorized: true,
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(2, $result->items);
        $titles = array_map(fn ($item) => $item->title, $result->items);
        $this->assertContains('Tech Post', $titles);
        $this->assertContains('Uncategorized Post', $titles);
        $this->assertNotContains('PHP Post', $titles);
    }

    public function test_execute_filters_by_category_ids_without_include_uncategorized(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $user = User::factory()->create();
        $postInTech = Post::factory()->create(['user_id' => $user->id, 'title' => 'Tech Post']);
        $postInTech->categories()->attach($tech->id);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Uncategorized Post']);
        $postInPhp = Post::factory()->create(['user_id' => $user->id, 'title' => 'PHP Post']);
        $postInPhp->categories()->attach($php->id);

        $dto = new PostIndexDto(
            userId: $user->id,
            page: 1,
            perPage: 10,
            categoryIds: [$tech->id],
            includeUncategorized: false,
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Tech Post', $result->items[0]->title);
    }

    public function test_execute_excludes_uncategorized_when_no_category_filter(): void
    {
        $tech = Category::factory()->create(['name' => 'Tech']);
        $user = User::factory()->create();
        $postCategorized = Post::factory()->create(['user_id' => $user->id, 'title' => 'Categorized Post']);
        $postCategorized->categories()->attach($tech->id);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Uncategorized Post']);

        $dto = new PostIndexDto(
            userId: $user->id,
            page: 1,
            perPage: 10,
            categoryIds: [],
            includeUncategorized: false,
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Categorized Post', $result->items[0]->title);
    }

    public function test_execute_filters_by_date_from(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Old Post', 'created_at' => '2023-06-01']);
        $recentPost = Post::factory()->create(['user_id' => $user->id, 'title' => 'Recent Post', 'created_at' => '2024-06-01']);

        $dto = new PostIndexDto(
            userId: $user->id,
            page: 1,
            perPage: 10,
            dateFrom: '2024-01-01',
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Recent Post', $result->items[0]->title);
    }

    public function test_execute_filters_by_date_to(): void
    {
        $user = User::factory()->create();
        $oldPost = Post::factory()->create(['user_id' => $user->id, 'title' => 'Old Post', 'created_at' => '2023-06-01']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Future Post', 'created_at' => '2025-06-01']);

        $dto = new PostIndexDto(
            userId: $user->id,
            page: 1,
            perPage: 10,
            dateTo: '2024-12-31',
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Old Post', $result->items[0]->title);
    }

    public function test_execute_sorts_by_date_asc(): void
    {
        $user = User::factory()->create();
        $old = Post::factory()->create(['user_id' => $user->id, 'title' => 'Oldest', 'created_at' => '2024-01-01']);
        $middle = Post::factory()->create(['user_id' => $user->id, 'title' => 'Middle', 'created_at' => '2024-06-01']);
        $newest = Post::factory()->create(['user_id' => $user->id, 'title' => 'Newest', 'created_at' => '2024-12-01']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, sort: 'date_asc');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(3, $result->items);
        $this->assertEquals('Oldest', $result->items[0]->title);
        $this->assertEquals('Middle', $result->items[1]->title);
        $this->assertEquals('Newest', $result->items[2]->title);
    }

    public function test_execute_sorts_by_comments_desc(): void
    {
        $user = User::factory()->create();
        $fewComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Few Comments']);
        $manyComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Many Comments']);
        Comment::factory()->count(5)->create(['post_id' => $manyComments->id, 'parent_id' => null]);
        Comment::factory()->count(1)->create(['post_id' => $fewComments->id, 'parent_id' => null]);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, sort: 'comments');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(2, $result->items);
        $this->assertEquals('Many Comments', $result->items[0]->title);
        $this->assertEquals('Few Comments', $result->items[1]->title);
    }

    public function test_execute_sorts_by_comments_asc(): void
    {
        $user = User::factory()->create();
        $fewComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Few Comments']);
        $manyComments = Post::factory()->create(['user_id' => $user->id, 'title' => 'Many Comments']);
        Comment::factory()->count(5)->create(['post_id' => $manyComments->id, 'parent_id' => null]);
        Comment::factory()->count(1)->create(['post_id' => $fewComments->id, 'parent_id' => null]);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, sort: 'comments_asc');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(2, $result->items);
        $this->assertEquals('Few Comments', $result->items[0]->title);
        $this->assertEquals('Many Comments', $result->items[1]->title);
    }

    public function test_execute_loads_user_when_load_user_true(): void
    {
        $user = User::factory()->create(['name' => 'Test Author']);
        $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Post With Author']);

        $dto = new PostIndexDto(
            userId: null,
            page: 1,
            perPage: 10,
            loadUser: true,
        );
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Test Author', $result->items[0]->userName);
    }

    public function test_build_prefix_tsquery_returns_null_for_empty_after_sanitization(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Some Post', 'content' => 'Content.']);

        $dto = new PostIndexDto(userId: $user->id, page: 1, perPage: 10, search: '!!!@@@');
        $service = new PostIndexService;
        $result = $service->execute($dto);

        $this->assertCount(1, $result->items);
        $this->assertEquals('Some Post', $result->items[0]->title);
    }
}
