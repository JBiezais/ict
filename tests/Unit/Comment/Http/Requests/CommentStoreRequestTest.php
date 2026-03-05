<?php

namespace Tests\Unit\Comment\Http\Requests;

use App\Comment\Database\Models\Comment;
use App\Comment\Http\Requests\CommentStoreRequest;
use App\Post\Database\Models\Post;
use App\Post\Http\Controllers\PostPublicController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class CommentStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new CommentStoreRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $post = Post::factory()->create();

        $request = CommentStoreRequest::create('/posts/1/comments', 'POST', ['content' => 'Comment content']);
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('POST', '/posts/{post}/comments', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:2000'], $rules['content']);
        $this->assertArrayHasKey('parent_id', $rules);
        $this->assertContains('nullable', $rules['parent_id']);
        $this->assertContains('integer', $rules['parent_id']);
        $existsRule = collect($rules['parent_id'])->first(fn ($r) => is_string($r) && str_contains($r, 'exists'));
        $this->assertNotNull($existsRule);
        $this->assertStringContainsString('exists:comments,id', $existsRule);
    }

    public function test_parent_id_rule_skips_check_when_route_post_is_not_post_model(): void
    {
        $comment = Comment::factory()->create();

        $request = CommentStoreRequest::create('/posts/1/comments', 'POST', [
            'content' => 'Test content',
            'parent_id' => (string) $comment->id,
        ]);
        $request->setContainer(app());
        $request->setRouteResolver(function () {
            $route = new Route('POST', '/posts/{post}/comments', fn () => null);
            $route->bind(request());
            $route->setParameter('post', 'not-a-post-model');

            return $route;
        });

        $request->validate($request->rules());
        $this->addToAssertionCount(1);
    }

    public function test_parent_id_rule_skips_check_when_value_is_not_numeric(): void
    {
        $post = Post::factory()->create();

        $request = CommentStoreRequest::create('/posts/'.$post->id.'/comments', 'POST', [
            'content' => 'Test content',
            'parent_id' => 'not-numeric',
        ]);
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('POST', '/posts/{post}/comments', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $request->validate($request->rules());
    }

    public function test_parent_id_rule_rejects_reply_when_parent_at_max_nesting_depth(): void
    {
        $post = Post::factory()->create();
        $current = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null]);
        for ($i = 0; $i < PostPublicController::MAX_COMMENT_NESTING_DEPTH; $i++) {
            $current = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $current->id]);
        }

        $request = CommentStoreRequest::create('/posts/'.$post->id.'/comments', 'POST', [
            'content' => 'Reply at max depth',
            'parent_id' => (string) $current->id,
        ]);
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('POST', '/posts/{post}/comments', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $request->validate($request->rules());
    }

    public function test_parent_id_rule_allows_reply_when_parent_below_max_nesting_depth(): void
    {
        $post = Post::factory()->create();
        $root = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null]);
        $child = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $root->id]);

        $request = CommentStoreRequest::create('/posts/'.$post->id.'/comments', 'POST', [
            'content' => 'Valid reply',
            'parent_id' => (string) $child->id,
        ]);
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($post) {
            $route = new Route('POST', '/posts/{post}/comments', fn () => null);
            $route->bind(request());
            $route->setParameter('post', $post);

            return $route;
        });

        $request->validate($request->rules());
        $this->addToAssertionCount(1);
    }
}
