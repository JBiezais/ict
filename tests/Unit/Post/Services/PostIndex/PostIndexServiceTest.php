<?php

namespace Tests\Unit\Post\Services\PostIndex;

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
}
