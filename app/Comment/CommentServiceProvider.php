<?php

namespace App\Comment;

use App\Comment\Database\Models\Comment;
use App\Comment\Policies\CommentPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::anonymousComponentPath(
            resource_path('views/comments/components'),
            'comments'
        );

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/CommentRoutes.php');

        Gate::policy(Comment::class, CommentPolicy::class);
    }
}
