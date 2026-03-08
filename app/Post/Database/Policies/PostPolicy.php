<?php

namespace App\Post\Database\Policies;

use App\Post\Database\Models\Post;
use App\User\Database\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }
}
