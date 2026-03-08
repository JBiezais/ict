<?php

namespace App\Auth\Services\ConfirmPassword;

use App\User\Database\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmPasswordService
{
    public function execute(User $user, string $password, Request $request): void
    {
        if (! Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());
    }
}
