<?php

namespace App\Auth\Http\Controllers;

use App\Auth\Http\Requests\ConfirmPasswordRequest;
use App\Auth\Services\ConfirmPassword\ConfirmPasswordService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.pages.confirm-password');
    }

    public function store(ConfirmPasswordRequest $request, ConfirmPasswordService $confirmPasswordService): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $confirmPasswordService->execute($user, $request->validated('password'), $request);

        return redirect()->intended(route('my-posts.posts.index', absolute: false));
    }
}
