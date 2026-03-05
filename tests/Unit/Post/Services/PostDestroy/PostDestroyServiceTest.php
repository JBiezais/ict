<?php

namespace Tests\Unit\Post\Services\PostDestroy;

use App\Post\Database\Models\Post;
use App\Post\Services\PostDestroy\PostDestroyService;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostDestroyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_deletes_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);

        $service = new PostDestroyService;
        $service->execute($post);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
