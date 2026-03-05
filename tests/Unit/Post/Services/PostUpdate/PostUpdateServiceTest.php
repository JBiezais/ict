<?php

namespace Tests\Unit\Post\Services\PostUpdate;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\Post\Services\PostUpdate\DTO\PostUpdateDto;
use App\Post\Services\PostUpdate\PostUpdateService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_updates_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'content' => 'Old content',
        ]);
        $dto = new PostUpdateDto(postId: $post->id, title: 'New Title', content: 'New content.', categoryIds: []);

        $service = new PostUpdateService;
        $service->execute($dto, $post);

        $post->refresh();
        $this->assertEquals('New Title', $post->title);
        $this->assertEquals('New content.', $post->content);
    }

    public function test_execute_syncs_categories(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post->categories()->attach($tech->id);

        $laravel = Category::factory()->create(['name' => 'Laravel']);
        $dto = new PostUpdateDto(
            postId: $post->id,
            title: $post->title,
            content: $post->content,
            categoryIds: [$laravel->id],
        );

        $service = new PostUpdateService;
        $service->execute($dto, $post);

        $post->refresh();
        $post->load('categories');
        $this->assertCount(1, $post->categories);
        $this->assertEquals('Laravel', $post->categories->first()->name);
    }

    public function test_execute_clears_categories_when_empty(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post->categories()->attach($tech->id);

        $dto = new PostUpdateDto(
            postId: $post->id,
            title: $post->title,
            content: $post->content,
            categoryIds: [],
        );

        $service = new PostUpdateService;
        $service->execute($dto, $post);

        $post->refresh();
        $post->load('categories');
        $this->assertCount(0, $post->categories);
    }
}
