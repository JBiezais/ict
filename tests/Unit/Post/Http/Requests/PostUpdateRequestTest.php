<?php

namespace Tests\Unit\Post\Http\Requests;

use App\Post\Database\Models\Post;
use App\Post\Http\Requests\PostUpdateRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class PostUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_allows_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $request = PostUpdateRequest::create('/my-posts/posts/1', 'PUT', ['title' => 'Title', 'content' => 'Content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('PUT', '/my-posts/posts/{post}', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $this->assertTrue($request->authorize());
    }

    public function test_authorize_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $request = PostUpdateRequest::create('/my-posts/posts/1', 'PUT', ['title' => 'Title', 'content' => 'Content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => $otherUser);
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('PUT', '/my-posts/posts/{post}', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_authorize_denies_unauthenticated_user(): void
    {
        $post = Post::factory()->create();

        $request = PostUpdateRequest::create('/my-posts/posts/1', 'PUT', ['title' => 'Title', 'content' => 'Content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => null);
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('PUT', '/my-posts/posts/{post}', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new PostUpdateRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:255'], $rules['title']);
        $this->assertEquals(['required', 'string'], $rules['content']);
    }
}
