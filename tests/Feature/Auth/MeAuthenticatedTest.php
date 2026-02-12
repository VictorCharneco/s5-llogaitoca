<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class MeAuthenticatedTest extends TestCase{
    use RefreshDatabase;

    public function test_me_returns_authenticated_user():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])
        ->assertJsonPath('user.email', 'test@example.com')
        ->assertJsonPath('user.role', 'user');
    }
    

}