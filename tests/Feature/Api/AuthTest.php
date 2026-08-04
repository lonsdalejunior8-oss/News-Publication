<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_issues_a_token(): void
    {
        $user = User::factory()->create(['role' => 'author']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
        $this->assertSame('author', $response->json('user.role'));
    }

    public function test_login_fails_with_bad_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_authenticated_user_endpoint_requires_a_token(): void
    {
        $this->getJson('/api/user')->assertStatus(401);
    }
}
