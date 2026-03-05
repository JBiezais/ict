<?php

namespace App\Comment\Services\CommentStore;

use App\Comment\Database\Models\Comment;
use App\Comment\Services\CommentStore\DTO\CommentStoreDto;

class CommentStoreService
{
    public function execute(CommentStoreDto $dto): Comment
    {
        return Comment::query()->create([
            'post_id' => $dto->postId,
            'user_id' => $dto->userId,
            'parent_id' => $dto->parentId,
            'content' => $dto->content,
        ]);
    }
}
