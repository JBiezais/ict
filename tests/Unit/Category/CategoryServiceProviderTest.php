<?php

namespace Tests\Unit\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_routes_are_registered(): void
    {
        $this->assertNotNull(\Illuminate\Support\Facades\Route::getRoutes()->getByName('categories.store'));
    }
}
