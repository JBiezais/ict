<?php

use App\Category\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('can:create,App\Category\Database\Models\Category')
        ->name('categories.store');
});
