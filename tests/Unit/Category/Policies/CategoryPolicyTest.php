<?php

namespace Tests\Unit\Category\Policies;

use App\Category\Policies\CategoryPolicy;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_allows_authenticated_user(): void
    {
        $user = User::factory()->create();
        $policy = new CategoryPolicy;

        $this->assertTrue($policy->create($user));
    }
}
