<?php

namespace App\Post\Services\PostUpdate;

use App\Post\Database\Models\Post;
use App\Post\Services\PostUpdate\DTO\PostUpdateDto;

class PostUpdateService
{
    public function execute(PostUpdateDto $dto, Post $post): void
    {
        $post->update([
            'title' => $dto->title,
            'content' => $dto->content,
        ]);
    }
}
