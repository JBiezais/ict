<?php

namespace Tests\Unit\Post\Services\PostIndex;

use App\Post\Database\Models\Post;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\PostIndexService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
