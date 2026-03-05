<?php

namespace App\Post;

use App\Post\Console\SeedPostsCommand;
use App\Post\Database\Models\Post;
use App\Post\Policies\PostPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::anonymousComponentPath(
            resource_path('views/posts/components'),
            'posts'
        );

        Blade::anonymousComponentPath(
            resource_path('views/posts/pages/manage/components'),
            'posts.pages.manage'
        );

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/PostRoutes.php');
        $this->commands([SeedPostsCommand::class]);

        Gate::policy(Post::class, PostPolicy::class);
    }
}
