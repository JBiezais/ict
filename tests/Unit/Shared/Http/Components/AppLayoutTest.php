<?php

namespace Tests\Unit\Shared\Http\Components;

use App\Shared\Components\BaseLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_app_layout_view(): void
    {
        $component = new BaseLayout;

        $view = $component->render();

        $this->assertEquals('layouts.base', $view->name());
    }
}
