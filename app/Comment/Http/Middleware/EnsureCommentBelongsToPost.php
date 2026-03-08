<?php

namespace App\Comment\Http\Middleware;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommentBelongsToPost
{
    public function handle(Request $request, Closure $next): Response
    {
        $post = $request->route('post');
        $comment = $request->route('comment');

        if ($post instanceof Post
            && $comment instanceof Comment
            && $comment->post_id !== $post->id) {
            abort(404);
        }

        $response = $next($request);

        return $response instanceof Response ? $response : response('');
    }
}
