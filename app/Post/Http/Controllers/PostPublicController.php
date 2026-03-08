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

    public function show(Post $post): View
    {
        $post->loadCount('comments');
        $post->load([
            'user',
            'categories',
            'comments' => fn (Relation $query): mixed => $query
                ->whereNull('parent_id')
                ->withCount('children')
                ->with('user')
                ->latest(),
        ]);

        return view('posts.pages.show', [
            'post' => $post,
            'maxCommentNestingDepth' => self::MAX_COMMENT_NESTING_DEPTH,
        ]);
    }
}
