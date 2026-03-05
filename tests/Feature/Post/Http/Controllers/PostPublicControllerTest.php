<?php

namespace Tests\Feature\Post\Http\Controllers;

use App\Post\Database\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPublicControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('posts.pages.browse');
    }

    public function test_post_show_page_can_be_rendered(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertViewIs('posts.pages.show');
    }

    public function test_post_show_displays_post_content(): void
    {
        $post = Post::factory()->create([
            'title' => 'My Test Post',
            'content' => 'This is the post content.',
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertSee('My Test Post');
        $response->assertSee('This is the post content.');
    }
}
