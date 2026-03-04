<?php

namespace App\Shared;

use App\Shared\Http\Components\AppLayout;
use App\Shared\Http\Components\GuestLayout;
use Illuminate\Support\ServiceProvider;

class SharedServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/SharedRoutes.php');

        $this->loadViewComponentsAs('shared', [
            AppLayout::class,
            GuestLayout::class,
        ]);
    }
}
