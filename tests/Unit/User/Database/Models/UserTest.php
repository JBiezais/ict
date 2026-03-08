<?php

namespace Tests\Unit\User\Database\Models;

use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        $this->assertEquals(['id', 'password', 'remember_token'], $user->getHidden());
    }

    public function test_casts_include_email_verified_at_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_posts_relationship_returns_has_many(): void
    {
        $user = new User;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->posts());
    }

    public function test_user_has_many_posts(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->posts);
    }

    public function test_comments_relationship_returns_has_many(): void
    {
        $user = new User;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->comments());
    }

    public function test_user_has_many_comments(): void
    {
        $user = User::factory()->create();
        Comment::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->comments);
    }

    public function test_uuid_is_auto_generated_on_create(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->uuid);
        $this->assertTrue(Str::isUuid($user->uuid));
    }

    public function test_get_route_key_name_returns_uuid(): void
    {
        $user = new User;

        $this->assertSame('uuid', $user->getRouteKeyName());
    }

    public function test_uuid_is_not_overwritten_when_provided(): void
    {
        $presetUuid = Str::uuid()->toString();
        $user = new User;
        $user->uuid = $presetUuid;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = Hash::make('password');
        $user->save();

        $this->assertSame($presetUuid, $user->fresh()->uuid);
    }
}
