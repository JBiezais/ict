<?php

namespace App\Post\Services\PostIndex\DTO;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class PostDto extends Data
{
    /**
     * @param  Collection<int, object{uuid: string, name: string}>  $categories
     */
    public function __construct(
        public readonly string $uuid,
        public readonly string $title,
        public readonly string $content,
        public readonly ?CarbonInterface $createdAt,
        public readonly int $commentsCount,
        public readonly Collection $categories,
        public readonly ?string $userName = null,
    ) {}
}
