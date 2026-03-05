<?php

namespace Tests\Unit\Category\Services\CategoryStore;

use App\Category\Database\Models\Category;
use App\Category\Services\CategoryStore\CategoryStoreService;
use App\Category\Services\CategoryStore\DTO\CategoryStoreDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryStoreServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_category(): void
    {
        $dto = new CategoryStoreDto(name: 'Tech');

        $service = new CategoryStoreService;
        $category = $service->execute($dto);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Tech', $category->name);
        $this->assertDatabaseHas('categories', ['name' => 'Tech']);
    }

    public function test_execute_trims_name(): void
    {
        $dto = new CategoryStoreDto(name: '  Laravel  ');

        $service = new CategoryStoreService;
        $category = $service->execute($dto);

        $this->assertEquals('Laravel', $category->name);
    }
}
