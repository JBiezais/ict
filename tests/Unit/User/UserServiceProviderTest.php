<?php

namespace Tests\Unit\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_migrations_are_registered(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
    }
}
