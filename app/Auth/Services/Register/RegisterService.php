<?php

namespace App\Auth\Services\Register;

use App\Auth\Services\Register\DTO\RegisterDto;
use App\User\Database\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterService
{
    public function execute(RegisterDto $dto): User
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => now(),
            'password' => $dto->password,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
