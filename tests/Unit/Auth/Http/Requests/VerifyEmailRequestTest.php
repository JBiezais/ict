<?php

namespace Tests\Unit\Auth\Http\Requests;

use App\Auth\Http\Requests\VerifyEmailRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class VerifyEmailRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_false_when_user_is_null(): void
    {
        $user = User::factory()->create();
        $request = VerifyEmailRequest::create(
            "/verify-email/{$user->uuid}/".sha1($user->email),
            'GET'
        );
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($user) {
            $route = new Route('GET', 'verify-email/{uuid}/{hash}', []);
            $route->bind(app('request'));
            $route->setParameter('uuid', $user->uuid);
            $route->setParameter('hash', sha1($user->email));

            return $route;
        });
        $request->setUserResolver(fn () => null);

        $this->assertFalse($request->authorize());
    }

    public function test_authorize_returns_false_when_uuid_does_not_match(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $request = VerifyEmailRequest::create(
            "/verify-email/{$otherUser->uuid}/".sha1($user->email),
            'GET'
        );
        $request->setContainer(app());
        $request->setRouteResolver(function () use ($otherUser, $user) {
            $route = new Route('GET', 'verify-email/{uuid}/{hash}', []);
            $route->bind(app('request'));
            $route->setParameter('uuid', $otherUser->uuid);
            $route->setParameter('hash', sha1($user->email));

            return $route;
        });
        $request->setUserResolver(fn () => $user);

        $this->assertFalse($request->authorize());
    }
}
