<?php

namespace App\Post\Http\Controllers;

use App\Post\Database\Models\Post;
use App\Post\Http\Requests\PostBrowseRequest;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\PostIndexService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
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
    public function index(PostBrowseRequest $request, PostIndexService $postIndexService): View
    {
        $dto = PostIndexDto::fromBrowseRequest($request);
        $result = $postIndexService->execute($dto);

        $posts = new LengthAwarePaginator(
            $result->items,
            $result->total,
            $result->perPage,
            $result->currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        $posts->appends(collect($request->query())->forget('_fragment')->all());

        if ($request->header('X-Requested-With') === 'XMLHttpRequest' && $request->boolean('_fragment')) {
            return view('posts.components.browse-list', compact('posts'));
        }

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

        $post->load(['user', 'categories', 'comments' => function (Relation $query) use ($loadChildrenRecursively): void {
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
