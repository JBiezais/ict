<?php

namespace Tests\Unit\Shared\Http\Components;

use App\Auth\Components\AuthenticationLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_guest_layout_view(): void
    {
        $component = new AuthenticationLayout;

        $view = $component->render();

        $this->assertEquals('auth.layout.authentication', $view->name());
    }
}
