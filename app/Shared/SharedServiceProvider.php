<?php

namespace App\Shared;

use App\Shared\Components\AppLayout;
use App\Shared\Components\BaseLayout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SharedServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        Blade::component(BaseLayout::class, 'base-layout');
        Blade::component(AppLayout::class, 'app-layout');
    }
}
