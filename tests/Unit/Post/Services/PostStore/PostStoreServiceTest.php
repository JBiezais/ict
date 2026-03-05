<?php

namespace Tests\Unit\Post\Services\PostStore;

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
        $dto = new PostStoreDto(userId: $user->id, title: 'Test Title', content: 'Test content here.');

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
}
