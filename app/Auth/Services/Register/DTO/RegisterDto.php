<?php

namespace App\Auth\Services\Register\DTO;

use App\Auth\Http\Requests\RegisterRequest;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class RegisterDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $name = $request->validated('name');
        if (! is_string($name)) {
            throw new InvalidArgumentException('Name must be a string.');
        }

        $email = $request->validated('email');
        if (! is_string($email)) {
            throw new InvalidArgumentException('Email must be a string.');
        }

        $password = $request->validated('password');
        if (! is_string($password)) {
            throw new InvalidArgumentException('Password must be a string.');
        }

        return new self(
            name: $name,
            email: $email,
            password: $password,
        );
    }
}
