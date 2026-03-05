<?php

namespace Tests\Unit\Post\Services\PostUpdate;

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
        $dto = new PostUpdateDto(postId: $post->id, title: 'New Title', content: 'New content.');

        $service = new PostUpdateService;
        $service->execute($dto, $post);

        $post->refresh();
        $this->assertEquals('New Title', $post->title);
        $this->assertEquals('New content.', $post->content);
    }
}
