<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_user_can_login_and_get_token(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('test123'),
            'role' => 'user',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);
        $response->assertJsonPath('user.email', 'test@example.com');
        $response->assertJsonPath('user.role', 'user');
    }

    public function test_login_rejected_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('CorrectPass!1234'),
            'role' => 'user',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'erroneo321',
        ]);

        $response->assertStatus(401)->assertJsonStructure(['message']);
    }
}