<?php

namespace App\Category\Policies;

use App\User\Database\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return true;
    }
}
