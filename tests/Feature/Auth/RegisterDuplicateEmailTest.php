<?php


namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class RegisterDuplicateEmailTest extends TestCase{
    use RefreshDatabase;

    public function test_register_rejects_duplicate_email(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $data = [
            'name' => 'User Two',
            'email' => 'test@example.com',
            'password' => 'StrongPass!1234',
            'password_confirmation' => 'StrongPass!1234',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }
}