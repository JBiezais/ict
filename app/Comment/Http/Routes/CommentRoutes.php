<?php

use App\Comment\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::put('/posts/{post}/comments/{comment}', [CommentController::class, 'update'])->name('posts.comments.update');
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])->name('posts.comments.destroy');
});
