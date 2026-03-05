<?php

namespace Tests\Unit\Post\Services\PostStore;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\Post\Services\PostStore\DTO\PostStoreDto;
use App\Post\Services\PostStore\PostStoreService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostStoreServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_post_with_correct_attributes(): void
    {
        $user = User::factory()->create();
        $dto = new PostStoreDto(userId: $user->id, title: 'Test Title', content: 'Test content here.', categoryIds: []);

        $service = new PostStoreService;
        $service->execute($dto);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Test Title',
            'content' => 'Test content here.',
        ]);
        $post = Post::first();
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Test Title', $post->title);
        $this->assertEquals('Test content here.', $post->content);
    }

    public function test_execute_creates_post_with_empty_categories(): void
    {
        $user = User::factory()->create();
        $dto = new PostStoreDto(userId: $user->id, title: 'Post', content: 'Content', categoryIds: []);

        $service = new PostStoreService;
        $service->execute($dto);

        $post = Post::first();
        $this->assertCount(0, $post->categories);
    }

    public function test_execute_attaches_existing_categories_by_id(): void
    {
        $user = User::factory()->create();
        $tech = Category::factory()->create(['name' => 'Tech']);
        $laravel = Category::factory()->create(['name' => 'Laravel']);
        $dto = new PostStoreDto(
            userId: $user->id,
            title: 'Post',
            content: 'Content',
            categoryIds: [$tech->id, $laravel->id],
        );

        $service = new PostStoreService;
        $service->execute($dto);

        $post = Post::first();
        $this->assertCount(2, $post->categories);
        $this->assertTrue($post->categories->contains('name', 'Tech'));
        $this->assertTrue($post->categories->contains('name', 'Laravel'));
    }
}
