<?php

namespace App\Comment\Http\Controllers;

use App\Comment\Database\Models\Comment;
use App\Comment\Http\Requests\CommentDestroyRequest;
use App\Comment\Http\Requests\CommentStoreRequest;
use App\Comment\Http\Requests\CommentUpdateRequest;
use App\Comment\Services\CommentDestroy\CommentDestroyService;
use App\Comment\Services\CommentStore\CommentStoreService;
use App\Comment\Services\CommentStore\DTO\CommentStoreDto;
use App\Comment\Services\CommentUpdate\CommentUpdateService;
use App\Comment\Services\CommentUpdate\DTO\CommentUpdateDto;
use App\Post\Database\Models\Post;
use App\Post\Http\Controllers\PostPublicController;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function replies(Post $post, Comment $comment, Request $request): View
    {
        $depth = max(1, min((int) $request->query('depth', 1), PostPublicController::MAX_COMMENT_NESTING_DEPTH));
        $maxDepth = PostPublicController::MAX_COMMENT_NESTING_DEPTH;

        $comment->load([
            'children' => fn ($q) => $q->with('user')->withCount('children')->latest(),
        ]);

        return view('comments.partials.replies', [
            'comments' => $comment->children,
            'post' => $post,
            'depth' => $depth,
            'maxDepth' => $maxDepth,
        ]);
    }

    public function store(
        Post $post,
        CommentStoreRequest $request,
        CommentStoreService $commentStoreService
    ): RedirectResponse {
        $dto = CommentStoreDto::fromRequest($request, $post);
        $comment = $commentStoreService->execute($dto);

        return redirect()
            ->back()
            ->with('status', __('Comment added.'))
            ->withFragment('comment-'.$comment->uuid);
    }

    public function update(
        Post $post,
        Comment $comment,
        CommentUpdateRequest $request,
        CommentUpdateService $commentUpdateService
    ): RedirectResponse {
        $dto = CommentUpdateDto::fromRequest($request);
        $commentUpdateService->execute($dto, $comment);

        return redirect()
            ->back()
            ->with('status', __('Comment updated.'))
            ->withFragment('comment-'.$comment->uuid);
    }

    public function destroy(
        Post $post,
        Comment $comment,
        CommentDestroyRequest $request,
        CommentDestroyService $commentDestroyService
    ): RedirectResponse {
        $commentDestroyService->execute($comment);

        return redirect()
            ->back()
            ->with('status', __('Comment deleted.'));
    }
}
