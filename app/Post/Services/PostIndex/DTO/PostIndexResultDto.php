<?php

namespace App\Post\Services\PostIndex\DTO;

use Spatie\LaravelData\Data;

class PostIndexResultDto extends Data
{
    /**
     * @param  array<array-key, PostDto>  $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly int $lastPage,
    ) {}
}
