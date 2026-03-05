<?php

namespace Tests\Unit\Comment\Services\CommentUpdate;

use App\Comment\Database\Models\Comment;
use App\Comment\Services\CommentUpdate\CommentUpdateService;
use App\Comment\Services\CommentUpdate\DTO\CommentUpdateDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_updates_comment(): void
    {
        $comment = Comment::factory()->create(['content' => 'Original content']);
        $dto = new CommentUpdateDto(content: 'Updated content');

        $service = new CommentUpdateService;
        $service->execute($dto, $comment);

        $comment->refresh();
        $this->assertEquals('Updated content', $comment->content);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated content',
        ]);
    }
}
