<?php

namespace App\Post\Services\PostIndex;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\Post\Services\PostIndex\DTO\PostDto;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\DTO\PostIndexResultDto;

class PostIndexService
{
    public function execute(PostIndexDto $dto): PostIndexResultDto
    {
        $query = Post::query();

        if ($dto->userId !== null) {
            $query->where('user_id', $dto->userId);
        }

        $query->with($dto->loadUser ? ['categories', 'user'] : ['categories'])
            ->withCount('comments');

        $allCategoryIds = Category::pluck('id')->all();
        $applyCategoryFilter = ! empty($dto->categoryIds) && count($dto->categoryIds) < count($allCategoryIds);
        if ($applyCategoryFilter) {
            $query->where(function ($q) use ($dto): void {
                $q->whereHas('categories', fn ($sub) => $sub->whereIn('id', $dto->categoryIds));
                if ($dto->includeUncategorized) {
                    $q->orWhereDoesntHave('categories');
                }
            });
        } elseif (! $dto->includeUncategorized) {
            $query->whereHas('categories');
        }

        if ($dto->dateFrom !== null) {
            $query->whereDate('created_at', '>=', $dto->dateFrom);
        }
        if ($dto->dateTo !== null) {
            $query->whereDate('created_at', '<=', $dto->dateTo);
        }

        match ($dto->sort) {
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'comments' => $query->orderByDesc('comments_count')->orderByDesc('created_at'),
            'comments_asc' => $query->orderBy('comments_count')->orderBy('created_at'),
            default => $query->latest(),
        };

        $paginator = $query->paginate($dto->perPage, ['*'], 'page', $dto->page);

        $items = $paginator->getCollection()
            ->map(fn (Post $post) => new PostDto(
                id: $post->id,
                title: $post->title,
                content: $post->content,
                userId: $post->user_id,
                createdAt: $post->created_at,
                commentsCount: $post->comments_count,
                categories: $post->categories,
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
