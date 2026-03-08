<?php

namespace App\Auth\Http\Controllers;

use App\Auth\Http\Requests\VerifyEmailRequest;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(VerifyEmailRequest $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended(route('my-posts.posts.index', absolute: false).'?verified=1');
        }

        $request->fulfill();

        return redirect()->intended(route('my-posts.posts.index', absolute: false).'?verified=1');
    }
}
