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
                Route::get('posts', [PostController::class, 'index'])->name('posts.index');
                Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
                Route::post('posts', [PostController::class, 'store'])->name('posts.store');
                Route::get('posts/{post}/edit', [PostController::class, 'edit'])
                    ->middleware('can:update,post')
                    ->name('posts.edit');
                Route::match(['put', 'patch'], 'posts/{post}', [PostController::class, 'update'])
                    ->middleware('can:update,post')
                    ->name('posts.update');
                Route::delete('posts/{post}', [PostController::class, 'destroy'])
                    ->middleware('can:delete,post')
                    ->name('posts.destroy');
            });
    });
