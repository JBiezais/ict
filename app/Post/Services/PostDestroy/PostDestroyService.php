<?php

namespace App\Post\Services\PostDestroy;

use App\Post\Database\Models\Post;

class PostDestroyService
{
    public function execute(Post $post): void
    {
        $post->delete();
    }
}
