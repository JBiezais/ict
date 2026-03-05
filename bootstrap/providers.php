<?php

use App\AppServiceProvider;
use App\Auth\AuthServiceProvider;
use App\Category\CategoryServiceProvider;
use App\Comment\CommentServiceProvider;
use App\Post\PostServiceProvider;
use App\Shared\SharedServiceProvider;
use App\User\UserServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    CategoryServiceProvider::class,
    CommentServiceProvider::class,
    PostServiceProvider::class,
    SharedServiceProvider::class,
    UserServiceProvider::class,
];
