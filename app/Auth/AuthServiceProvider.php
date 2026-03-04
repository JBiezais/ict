<?php

namespace App\Auth;

use App\Auth\Components\AuthenticationLayout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/Routes/AuthRoutes.php');

        Blade::component(AuthenticationLayout::class, 'authentication-layout');
    }
}
