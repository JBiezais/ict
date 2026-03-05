<?php

namespace App\Post\Services\PostStore;

use App\Post\Database\Models\Post;
use App\Post\Services\PostStore\DTO\PostStoreDto;

class PostStoreService
{
    public function execute(PostStoreDto $dto): void
    {
        $post = Post::query()->create([
            'user_id' => $dto->userId,
            'title' => $dto->title,
            'content' => $dto->content,
        ]);

        $post->categories()->sync($dto->categoryIds);
    }
}
