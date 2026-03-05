<?php

namespace Tests\Unit;

use App\AppServiceProvider;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_register_does_not_throw(): void
    {
        $provider = $this->app->getProvider(AppServiceProvider::class);

        $provider->register();

        $this->assertTrue(true);
    }

    public function test_boot_does_not_throw(): void
    {
        $provider = $this->app->getProvider(AppServiceProvider::class);

        $provider->boot();

        $this->assertTrue(true);
    }
}
