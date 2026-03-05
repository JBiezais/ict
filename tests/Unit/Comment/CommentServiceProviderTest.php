<?php

namespace Tests\Unit\Comment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('posts.comments.store'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('posts.comments.update'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('posts.comments.destroy'));
    }
}
