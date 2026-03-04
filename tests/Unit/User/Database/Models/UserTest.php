<?php

namespace Tests\Unit\User\Database\Models;

use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $user = new User;

        $this->assertEquals(['name', 'email', 'password'], $user->getFillable());
    }

    public function test_hidden_attributes(): void
    {
        $user = new User;

        $this->assertEquals(['password', 'remember_token'], $user->getHidden());
    }

    public function test_casts_include_email_verified_at_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
