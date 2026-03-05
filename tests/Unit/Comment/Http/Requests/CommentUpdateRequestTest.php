<?php

namespace Tests\Unit\Comment\Http\Requests;

use App\Comment\Database\Models\Comment;
use App\Comment\Http\Requests\CommentUpdateRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class CommentUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_allows_owner(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $request = CommentUpdateRequest::create('/posts/1/comments/1', 'PUT', ['content' => 'Updated content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(function () use ($comment) {
            $route = new Route('PUT', '/posts/{post}/comments/{comment}', fn () => null);
            $route->bind(request());
            $route->setParameter('comment', $comment);

            return $route;
        });

        $this->assertTrue($request->authorize());
    }

    public function test_authorize_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $owner->id]);

        $request = CommentUpdateRequest::create('/posts/1/comments/1', 'PUT', ['content' => 'Updated content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => $otherUser);
        $request->setRouteResolver(function () use ($comment) {
            $route = new Route('PUT', '/posts/{post}/comments/{comment}', fn () => null);
            $route->bind(request());
            $route->setParameter('comment', $comment);

            return $route;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_authorize_denies_unauthenticated_user(): void
    {
        $comment = Comment::factory()->create();

        $request = CommentUpdateRequest::create('/posts/1/comments/1', 'PUT', ['content' => 'Updated content']);
        $request->setContainer(app());
        $request->setUserResolver(fn () => null);
        $request->setRouteResolver(function () use ($comment) {
            $route = new Route('PUT', '/posts/{post}/comments/{comment}', fn () => null);
            $route->bind(request());
            $route->setParameter('comment', $comment);

            return $route;
        });

        $this->assertFalse($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new CommentUpdateRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:2000'], $rules['content']);
    }
}
