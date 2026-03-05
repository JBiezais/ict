<?php

namespace App\Post;

use App\Category\Database\Models\Category;
use App\Post\Console\SeedPostsCommand;
use App\Post\Database\Models\Post;
use App\Post\Policies\PostPolicy;
use App\Post\View\Data\PostFilterBarData;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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

        View::composer(
            ['posts.pages.browse', 'posts.pages.manage.index'],
            function (\Illuminate\Contracts\View\View $view): void {
                $request = request();
                $categories = Category::orderBy('name')->get();
                $baseUrl = $request->routeIs('home')
                    ? route('home')
                    : route('my-posts.posts.index');
                $currentFilters = PostFilterBarData::currentFiltersFromRequest($request);
                $filterBarData = PostFilterBarData::fromFiltersAndCategories($currentFilters, $categories);
                $view->with(compact('categories', 'filterBarData', 'baseUrl'));
            }
        );

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/PostRoutes.php');
        $this->commands([SeedPostsCommand::class]);

        Gate::policy(Post::class, PostPolicy::class);
    }
}
