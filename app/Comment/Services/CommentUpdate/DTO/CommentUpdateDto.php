<?php

namespace App\Comment\Services\CommentUpdate\DTO;

use App\Comment\Http\Requests\CommentUpdateRequest;
use Spatie\LaravelData\Data;

class CommentUpdateDto extends Data
{
    public function __construct(
        public readonly string $content,
    ) {}

    public static function fromRequest(CommentUpdateRequest $request): self
    {
        $content = $request->validated('content');
        if (! is_string($content)) {
            throw new \InvalidArgumentException('Content must be a string.');
        }

        return new self(content: $content);
    }
}
