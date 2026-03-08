<?php

namespace App\Auth\Services\Login;

use App\Auth\Services\Login\DTO\LoginDto;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginService
{
    private const MAX_ATTEMPTS = 5;

    public function execute(LoginDto $dto, Request $request): void
    {
        $this->ensureIsNotRateLimited($dto->throttleKey, $request);

        if (! Auth::attempt(
            ['email' => $dto->email, 'password' => $dto->password],
            $dto->remember
        )) {
            RateLimiter::hit($dto->throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($dto->throttleKey);
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(string $throttleKey, Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]),
        ]);
    }
}
