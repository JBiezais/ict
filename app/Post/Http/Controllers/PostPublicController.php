<?php

namespace App\Post\Http\Controllers;

use App\Post\Database\Models\Post;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\View\View;

class PostPublicController extends Controller
{
    /**
     * Maximum depth for recursive comment loading to prevent overload.
     * Depth 0 = root comments; their children are depth 1, etc.
     */
    public const MAX_COMMENT_NESTING_DEPTH = 5;

    /**
     * Display a listing of all posts (public homepage).
     */
    public function index(): View
    {
        $posts = Post::with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return view('posts.pages.browse', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->loadCount('comments');
        $maxDepth = self::MAX_COMMENT_NESTING_DEPTH;
        $loadChildrenRecursively = function (Relation $query, int $currentDepth = 0) use (&$loadChildrenRecursively, $maxDepth): void {
            $query->withCount('children')
                ->with([
                    'user',
                    'children' => $currentDepth < $maxDepth
                        ? fn (Relation $q) => $loadChildrenRecursively($q, $currentDepth + 1)
                        : fn (Relation $q) => $q->with('user')->withCount('children'),
                ]);
        };

        $post->load(['user', 'comments' => function (Relation $query) use ($loadChildrenRecursively): void {
            $query->whereNull('parent_id')
                ->withCount('children')
                ->with(['user', 'children' => fn (Relation $q) => $loadChildrenRecursively($q, 1)]);
        }]);

        return view('posts.pages.show', [
            'post' => $post,
            'maxCommentNestingDepth' => self::MAX_COMMENT_NESTING_DEPTH,
        ]);
    }
}
