<?php

namespace Tests\Unit\User\Http\Requests;

use App\User\Database\Models\User;
use App\User\Http\Requests\ProfileUpdateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_rules_return_correct_validation_rules(): void
    {
        $user = User::factory()->create();

        $request = ProfileUpdateRequest::create('/profile', 'PATCH', [
            'name' => 'Test User',
            'email' => 'new@example.com',
        ]);
        $request->setUserResolver(fn () => $user);
        $request->setContainer(app());

        $rules = $request->rules();

        $this->assertEquals(['required', 'string', 'max:255'], $rules['name']);
        $this->assertIsArray($rules['email']);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('string', $rules['email']);
        $this->assertContains('lowercase', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
    }
}
