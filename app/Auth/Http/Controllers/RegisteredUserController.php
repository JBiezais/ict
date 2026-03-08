<?php

namespace App\Auth\Http\Controllers;

use App\Auth\Http\Requests\RegisterRequest;
use App\Auth\Services\Register\DTO\RegisterDto;
use App\Auth\Services\Register\RegisterService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.pages.register');
    }

    public function store(RegisterRequest $request, RegisterService $registerService): RedirectResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $registerService->execute($dto);

        return redirect(route('my-posts.posts.index', absolute: false));
    }
}
