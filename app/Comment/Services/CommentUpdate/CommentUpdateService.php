<?php

namespace App\Comment\Services\CommentUpdate;

use App\Comment\Database\Models\Comment;
use App\Comment\Services\CommentUpdate\DTO\CommentUpdateDto;

class CommentUpdateService
{
    public function execute(CommentUpdateDto $dto, Comment $comment): void
    {
        $comment->update([
            'content' => $dto->content,
        ]);
    }
}
