<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class LoginTest extends TestCase{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('test123'),
            'role' => 'user',
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'test123',
        ];

        $response = $this->postJson('/api/login', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);

        $response->assertJsonPath('user.email', 'test@example.com');
        $response->assertJsonPath('user.role', 'user');
    }

}
