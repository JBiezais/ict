<?php

namespace App\Post\Services\PostIndex\DTO;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class PostDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly int $userId,
        public readonly CarbonInterface $createdAt,
        public readonly int $commentsCount,
    ) {}
}
