<?php

namespace App\Auth\Services\Login\DTO;

use App\Auth\Http\Requests\LoginRequest;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class LoginDto extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember,
        public readonly string $throttleKey,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        $email = $request->validated('email');
        if (! is_string($email)) {
            throw new InvalidArgumentException('Email must be a string.');
        }

        $password = $request->validated('password');
        if (! is_string($password)) {
            throw new InvalidArgumentException('Password must be a string.');
        }

        $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

        return new self(
            email: $email,
            password: $password,
            remember: $request->boolean('remember'),
            throttleKey: $throttleKey,
        );
    }
}
