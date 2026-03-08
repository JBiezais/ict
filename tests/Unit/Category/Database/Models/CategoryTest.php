<?php

namespace Tests\Unit\Category\Database\Models;

use App\Category\Database\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_uuid_is_auto_generated_on_create(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $this->assertNotEmpty($category->uuid);
        $this->assertTrue(Str::isUuid($category->uuid));
    }

    public function test_get_route_key_name_returns_uuid(): void
    {
        $category = new Category;

        $this->assertSame('uuid', $category->getRouteKeyName());
    }

    public function test_uuid_is_not_overwritten_when_provided(): void
    {
        $presetUuid = Str::uuid()->toString();
        $category = new Category;
        $category->uuid = $presetUuid;
        $category->name = 'Test';
        $category->save();

        $this->assertSame($presetUuid, $category->fresh()->uuid);
    }
}
