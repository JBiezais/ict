<?php

use App\Post\Http\Controllers\PostController;
use App\Post\Http\Controllers\PostPublicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->group(function () {
        Route::get('/', [PostPublicController::class, 'index'])->name('home');
        Route::get('/posts/{post}', [PostPublicController::class, 'show'])->name('posts.show');

        Route::middleware(['auth'])
            ->prefix('my-posts')
            ->name('my-posts.')
            ->group(function () {
                Route::resource('posts', PostController::class)->except(['show']);
            });
    });
