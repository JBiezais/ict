<?php

namespace App\Category\Database\Policies;

use App\User\Database\Models\User;

class CategoryPolicy
{
    public function create(User $user): bool
    {
        return true;
    }
}
