<?php

namespace App\Post\Services\PostIndex;

use App\Post\Database\Models\Post;
use App\Post\Database\QueryBuilders\PostQueryBuilder;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\DTO\PostIndexResultDto;

class PostIndexService
{
    public function __construct(
        protected PostIndexResultMapper $resultMapper,
    ) {}

    public function execute(PostIndexDto $dto): PostIndexResultDto
    {
        /** @var PostQueryBuilder $query */
        $query = Post::query();

        $columns = $dto->search !== null && $dto->search !== '' ? null : ['*'];

        $paginator = $query
            ->filterByUser($dto->userId)
            ->loadForIndex($dto->loadUser)
            ->filterByCategories($dto->categoryIds, $dto->includeUncategorized, $dto->onlyUncategorizedExplicit)
            ->filterByDateRange($dto->dateFrom, $dto->dateTo)
            ->searchByFullText($dto->search)
            ->orderBySort($dto->sort)
            ->paginate($dto->perPage, $columns ?? ['*'], 'page', $dto->page);

        return $this->resultMapper->map($paginator);
    }
}
