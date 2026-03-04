<?php

use App\AppServiceProvider;
use App\Auth\AuthServiceProvider;
use App\Shared\SharedServiceProvider;
use App\User\UserServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    SharedServiceProvider::class,
    UserServiceProvider::class,
];
