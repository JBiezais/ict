<?php

namespace App\Post\Services\PostIndex;

use App\Post\Database\Models\Post;
use App\Post\Services\PostIndex\DTO\PostDto;
use App\Post\Services\PostIndex\DTO\PostIndexResultDto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PostIndexResultMapper
{
    /**
     * @param  LengthAwarePaginator<int, Post>  $paginator
     */
    public function map(LengthAwarePaginator $paginator): PostIndexResultDto
    {
        /** @var Collection<int, Post> $collection */
        $collection = $paginator->getCollection();

        $items = $collection
            ->map(fn (Post $post) => new PostDto(
                uuid: $post->uuid,
                title: $post->title,
                content: $post->content,
                createdAt: $post->created_at,
                commentsCount: $post->comments_count,
                categories: $post->categories->map(fn ($c) => (object) ['uuid' => $c->uuid, 'name' => $c->name]),
                userName: $post->relationLoaded('user') ? $post->user?->name : null,
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
