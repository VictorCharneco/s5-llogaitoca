<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class DeleteUserTest extends TestCase {
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_delete_user_requires_authentication(): void{
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }

    public function test_user_cant_delete_own_account(): void{
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $user->id,]);
    }

    public function test_user_cant_delete_other_user(): void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $other = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/users/{$other->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_any_user(): void{

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $userToBeDeleted = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/users/{$userToBeDeleted->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $userToBeDeleted->id,
        ]);
    }

    public function test_delete_user_returns_404_if_not_found(): void{

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/users/1985");

        $response->assertStatus(404);
    }
}