<?php

namespace App\Auth\Http\Controllers;

use App\Auth\Http\Requests\LoginRequest;
use App\Auth\Services\Login\DTO\LoginDto;
use App\Auth\Services\Login\LoginService;
use App\Auth\Services\Logout\LogoutService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.pages.login');
    }

    public function store(LoginRequest $request, LoginService $loginService): RedirectResponse
    {
        $dto = LoginDto::fromRequest($request);
        $loginService->execute($dto, $request);

        $request->session()->regenerate();

        return redirect()->intended(route('my-posts.posts.index', absolute: false));
    }

    public function destroy(Request $request, LogoutService $logoutService): RedirectResponse
    {
        $logoutService->execute($request);

        return redirect('/');
    }
}
