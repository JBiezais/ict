<?php

namespace App\Post\Http\Controllers;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\Post\Http\Requests\PostDestroyRequest;
use App\Post\Http\Requests\PostEditRequest;
use App\Post\Http\Requests\PostIndexRequest;
use App\Post\Http\Requests\PostStoreRequest;
use App\Post\Http\Requests\PostUpdateRequest;
use App\Post\Services\PostDestroy\PostDestroyService;
use App\Post\Services\PostIndex\DTO\PostIndexDto;
use App\Post\Services\PostIndex\PostIndexService;
use App\Post\Services\PostStore\DTO\PostStoreDto;
use App\Post\Services\PostStore\PostStoreService;
use App\Post\Services\PostUpdate\DTO\PostUpdateDto;
use App\Post\Services\PostUpdate\PostUpdateService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the current user's posts.
     */
    public function index(PostIndexRequest $request, PostIndexService $postIndexService): View
    {
        $dto = PostIndexDto::fromRequest($request);
        $result = $postIndexService->execute($dto);

        $posts = new LengthAwarePaginator(
            $result->items,
            $result->total,
            $result->perPage,
            $result->currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        $posts->withQueryString();

        $categories = Category::orderBy('name')->get();
        $currentFilters = [
            'category_ids' => $dto->categoryIds,
            'include_uncategorized' => $dto->includeUncategorized,
            'date_from' => $dto->dateFrom,
            'date_to' => $dto->dateTo,
            'sort' => $dto->sort,
        ];

        return view('posts.pages.manage.index', compact('posts', 'categories', 'currentFilters'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.pages.create', compact('categories'));
    }

    /**
     * Store a newly created post.
     */
    public function store(PostStoreRequest $request, PostStoreService $postStoreService): RedirectResponse
    {
        $dto = PostStoreDto::fromRequest($request);
        $postStoreService->execute($dto);

        return redirect()
            ->route('my-posts.posts.index')
            ->with('status', __('Post created successfully.'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(PostEditRequest $request, Post $post): View
    {
        $post->load('categories');
        $categories = Category::orderBy('name')->get();

        return view('posts.pages.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post.
     */
    public function update(PostUpdateRequest $request, Post $post, PostUpdateService $postUpdateService): RedirectResponse
    {
        $dto = PostUpdateDto::fromRequest($request, $post);
        $postUpdateService->execute($dto, $post);

        return redirect()
            ->route('my-posts.posts.index')
            ->with('status', __('Post updated successfully.'));
    }

    /**
     * Remove the specified post.
     */
    public function destroy(PostDestroyRequest $request, Post $post, PostDestroyService $postDestroyService): RedirectResponse
    {
        $postDestroyService->execute($post);

        return redirect()
            ->route('my-posts.posts.index')
            ->with('status', __('Post deleted successfully.'));
    }
}
