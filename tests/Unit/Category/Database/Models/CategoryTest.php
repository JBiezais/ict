<?php

namespace Tests\Unit\Category\Database\Models;

use App\Category\Database\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $category = new Category;

        $this->assertEquals(['name'], $category->getFillable());
    }

    public function test_posts_relationship_returns_belongs_to_many(): void
    {
        $category = new Category;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $category->posts());
    }

    public function test_new_factory_returns_category_factory(): void
    {
        $factory = Category::newFactory();

        $this->assertInstanceOf(\Database\Factories\CategoryFactory::class, $factory);
    }
}
