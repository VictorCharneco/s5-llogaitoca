<?php

namespace Tests\Feature\Users;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class UserShowAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_users_requires_authentication():void{
        $user = User::factory()->create();
        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }

    public function test_user_show_requires_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $token = $user1->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/users/{$user2->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_show_user(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'email' => 'user@example.com']);

        $token = $admin->createToken('api-token')->accessToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ])
        ->assertJsonPath('data.email', 'user@example.com')
        ->assertJsonPath('data.role', 'user');
    }

    public function test_admin_return_404_if_user_not_found():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/user/1985");
        
        $response->assertStatus(404);
    }

}