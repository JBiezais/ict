<?php

namespace Tests\Unit\Shared\Http\Components;

use App\Shared\Http\Components\AppLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_app_layout_view(): void
    {
        $component = new AppLayout;

        $view = $component->render();

        $this->assertEquals('layouts.app', $view->name());
    }
}
