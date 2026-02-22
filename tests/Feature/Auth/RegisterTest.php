<?php


namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class RegisterTest extends TestCase{
    use RefreshDatabase;

    public function test_user_can_register_and_get_token(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'StrongPass!1234',
            'password_confirmation' => 'StrongPass!1234',
        ];

        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);

        $response->assertJsonPath('user.email', 'test@example.com');
        $response->assertJsonPath('user.role', 'user');
        $response->assertJsonMissing(['password']);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
    }
}