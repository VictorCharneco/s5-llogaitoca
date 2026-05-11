<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/me');

        $response->assertStatus(200)->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);
        $response->assertJsonPath('user.email', 'test@example.com');
        $response->assertJsonPath('user.role', 'user');
    }
}