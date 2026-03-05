<?php

namespace Tests\Unit\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('home'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('posts.show'));
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('my-posts.posts.index'));
    }

    public function test_post_seed_command_is_registered(): void
    {
        $this->artisan('post:seed', ['--count' => 0])->assertSuccessful();
    }
}
