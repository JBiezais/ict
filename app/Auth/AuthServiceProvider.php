<?php

namespace App\Auth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/Routes/AuthRoutes.php');
    }
}
