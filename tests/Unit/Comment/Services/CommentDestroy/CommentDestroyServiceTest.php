<?php

namespace Tests\Unit\Comment\Services\CommentDestroy;

use App\Comment\Database\Models\Comment;
use App\Comment\Services\CommentDestroy\CommentDestroyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentDestroyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_deletes_comment(): void
    {
        $comment = Comment::factory()->create();

        $service = new CommentDestroyService;
        $service->execute($comment);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
