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
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
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
            ->withFragment('comment-'.$comment->id);
    }

    /**
     * Update the specified comment.
     */
    public function update(
        Post $post,
        Comment $comment,
        CommentUpdateRequest $request,
        CommentUpdateService $commentUpdateService
    ): RedirectResponse {
        $this->authorizeCommentBelongsToPost($post, $comment);

        $dto = CommentUpdateDto::fromRequest($request);
        $commentUpdateService->execute($dto, $comment);

        return redirect()
            ->back()
            ->with('status', __('Comment updated.'))
            ->withFragment('comment-'.$comment->id);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(
        Post $post,
        Comment $comment,
        CommentDestroyRequest $request,
        CommentDestroyService $commentDestroyService
    ): RedirectResponse {
        $this->authorizeCommentBelongsToPost($post, $comment);

        $commentDestroyService->execute($comment);

        return redirect()
            ->back()
            ->with('status', __('Comment deleted.'));
    }

    private function authorizeCommentBelongsToPost(Post $post, Comment $comment): void
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }
    }
}
