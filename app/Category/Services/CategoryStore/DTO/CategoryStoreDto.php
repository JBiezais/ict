<?php

namespace App\Category\Services\CategoryStore\DTO;

use App\Category\Http\Requests\CategoryStoreRequest;
use Spatie\LaravelData\Data;

class CategoryStoreDto extends Data
{
    public function __construct(
        public readonly string $name,
    ) {}

    public static function fromRequest(CategoryStoreRequest $request): self
    {
        $name = $request->validated('name');
        if (! is_string($name)) {
            throw new \InvalidArgumentException('Name must be a string.');
        }

        return new self(name: trim($name));
    }
}
