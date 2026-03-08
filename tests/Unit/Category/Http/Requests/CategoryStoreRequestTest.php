<?php

namespace Tests\Unit\Category\Http\Requests;

use App\Category\Http\Requests\CategoryStoreRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new CategoryStoreRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_return_correct_validation_rules(): void
    {
        $request = new CategoryStoreRequest;

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:100', 'unique:categories,name'], $rules['name']);
    }

    public function test_validation_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('categories.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
