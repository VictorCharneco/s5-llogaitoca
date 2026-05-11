<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_user_can_register_and_get_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'StrongPass!1234',
            'password_confirmation' => 'StrongPass!1234',
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);
        $response->assertJsonPath('user.email', 'test@example.com');
        $response->assertJsonPath('user.role', 'user');
        $response->assertJsonMissing(['password']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'user']);
    }

    public function test_register_requires_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'password' => 'test123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_weak_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'weak@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'User Two',
            'email' => 'test@example.com',
            'password' => 'StrongPass!1234',
            'password_confirmation' => 'StrongPass!1234',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }
}