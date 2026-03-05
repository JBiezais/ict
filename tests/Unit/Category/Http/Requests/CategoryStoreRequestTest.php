<?php

namespace Tests\Unit\Category\Http\Requests;

use App\Category\Http\Requests\CategoryStoreRequest;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CategoryStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_false_for_guest(): void
    {
        $request = CategoryStoreRequest::createFrom(Request::create(route('categories.store'), 'POST', ['name' => 'Test']))
            ->setContainer($this->app);

        $this->assertFalse($request->authorize());
    }

    public function test_authorize_returns_true_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $request = CategoryStoreRequest::createFrom(Request::create(route('categories.store'), 'POST', ['name' => 'Test']))
            ->setContainer($this->app);
        $request->setUserResolver(fn () => $user);

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
