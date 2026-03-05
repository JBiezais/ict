<?php

namespace App\Post\Services\PostIndex;

use App\Post\Database\Models\Post;
use App\Post\Services\PostIndex\DTO\PostDto;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\DTO\PostIndexResultDto;

class PostIndexService
{
    public function execute(PostIndexDto $dto): PostIndexResultDto
    {
        $paginator = Post::where('user_id', $dto->userId)
            ->with('categories')
            ->withCount('comments')
            ->latest()
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);

        $items = $paginator->getCollection()
            ->map(fn (Post $post) => new PostDto(
                id: $post->id,
                title: $post->title,
                content: $post->content,
                userId: $post->user_id,
                createdAt: $post->created_at,
                commentsCount: $post->comments_count,
                categories: $post->categories,
            ))
            ->values()
            ->all();

        return new PostIndexResultDto(
            items: $items,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
        );
    }
}
