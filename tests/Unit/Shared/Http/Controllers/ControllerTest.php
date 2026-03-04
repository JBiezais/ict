<?php

namespace Tests\Unit\Shared\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Auth\Http\Controllers\AuthenticatedSessionController;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function test_base_controller_can_be_extended(): void
    {
        $controller = new AuthenticatedSessionController;

        $this->assertInstanceOf(Controller::class, $controller);
    }
}
