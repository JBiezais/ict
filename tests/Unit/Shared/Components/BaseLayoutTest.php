<?php

namespace Tests\Unit\Shared\Components;

use App\Shared\Components\BaseLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaseLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_base_layout_view(): void
    {
        $component = new BaseLayout;

        $view = $component->render();

        $this->assertEquals('layouts.base', $view->name());
    }
}
