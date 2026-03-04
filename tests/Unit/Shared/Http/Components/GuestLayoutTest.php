<?php

namespace Tests\Unit\Shared\Http\Components;

use App\Shared\Http\Components\GuestLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_guest_layout_view(): void
    {
        $component = new GuestLayout;

        $view = $component->render();

        $this->assertEquals('layouts.guest', $view->name());
    }
}
