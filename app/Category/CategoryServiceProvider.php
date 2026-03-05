<?php

namespace App\Category;

use App\Category\Database\Models\Category;
use App\Category\Policies\CategoryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/CategoryRoutes.php');

        Gate::policy(Category::class, CategoryPolicy::class);
    }
}
