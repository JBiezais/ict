<?php

namespace App\Comment\Services\CommentDestroy;

use App\Comment\Database\Models\Comment;

class CommentDestroyService
{
    public function execute(Comment $comment): void
    {
        $comment->delete();
    }
}
