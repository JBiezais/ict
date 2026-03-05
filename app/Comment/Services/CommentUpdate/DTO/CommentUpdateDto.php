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

        return new self(content: $content);
    }
}
