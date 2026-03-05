<?php

use App\AppServiceProvider;
use App\Auth\AuthServiceProvider;
use App\Post\PostServiceProvider;
use App\Shared\SharedServiceProvider;
use App\User\UserServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    PostServiceProvider::class,
    SharedServiceProvider::class,
    UserServiceProvider::class,
];
