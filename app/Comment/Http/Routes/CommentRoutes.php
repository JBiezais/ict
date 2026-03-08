<?php

use App\Comment\Http\Controllers\CommentController;
use App\Comment\Http\Middleware\EnsureCommentBelongsToPost;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/posts/{post}/comments/{comment}/replies', [CommentController::class, 'replies'])
        ->middleware([EnsureCommentBelongsToPost::class])
        ->name('posts.comments.replies');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::put('/posts/{post}/comments/{comment}', [CommentController::class, 'update'])
        ->middleware([EnsureCommentBelongsToPost::class, 'can:update,comment'])
        ->name('posts.comments.update');
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])
        ->middleware([EnsureCommentBelongsToPost::class, 'can:delete,comment'])
        ->name('posts.comments.destroy');
});
