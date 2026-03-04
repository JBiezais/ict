<?php

namespace Tests\Unit;

use App\AppServiceProvider;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_register_does_not_throw(): void
    {
        $app = $this->createMock(Application::class);
        $provider = new AppServiceProvider($app);

        $provider->register();

        $this->assertTrue(true);
    }

    public function test_boot_does_not_throw(): void
    {
        $app = $this->createMock(Application::class);
        $provider = new AppServiceProvider($app);

        $provider->boot();

        $this->assertTrue(true);
    }
}
