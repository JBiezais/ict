<?php

namespace Tests\Feature\Category\Http\Controllers;

use App\Category\Database\Models\Category;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_store_denies_guest(): void
    {
        $response = $this->postJson(route('categories.store'), ['name' => 'PHP']);

        $response->assertStatus(401);
    }

    public function test_categories_store_returns_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('categories.store'), [
            'name' => 'PHP',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['name' => 'PHP']);
        $response->assertJsonStructure(['id', 'name']);
        $this->assertDatabaseHas('categories', ['name' => 'PHP']);
    }

    public function test_categories_store_fails_duplicate(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'Tech']);

        $response = $this->actingAs($user)->postJson(route('categories.store'), [
            'name' => 'Tech',
        ]);

        $response->assertStatus(422);
    }
}
