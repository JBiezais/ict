<?php

namespace App\Post\Http\Controllers;

use App\Post\Database\Models\Post;
use App\Shared\Http\Controllers\Controller;
use Illuminate\View\View;

class PostPublicController extends Controller
{
    /**
     * Display a listing of all posts (public homepage).
     */
    public function index(): View
    {
        $posts = Post::with('user')
            ->latest()
            ->paginate(10);

        return view('posts.pages.browse', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load('user');

        return view('posts.pages.show', compact('post'));
    }
}
