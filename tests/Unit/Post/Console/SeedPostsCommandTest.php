<?php

namespace Tests\Unit\Post\Console;

use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedPostsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_posts_with_default_count(): void
    {
        $this->artisan('post:seed')
            ->assertSuccessful();

        $this->assertEquals(50, Post::count());
    }

    public function test_creates_posts_with_custom_count(): void
    {
        $this->artisan('post:seed', ['--count' => 5])
            ->assertSuccessful();

        $this->assertEquals(5, Post::count());
    }

    public function test_assigns_posts_to_user_when_user_option_provided(): void
    {
        $user = User::factory()->create();

        $this->artisan('post:seed', ['--user' => (string) $user->id, '--count' => 3])
            ->assertSuccessful();

        $this->assertEquals(3, Post::where('user_id', $user->id)->count());
    }
}
