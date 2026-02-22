<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class RegisterValidationTest extends TestCase{
    use RefreshDatabase;

    public function test_register_requires_email():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $data = [
            'name' => 'Test USer',
            'password' => 'test123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_weak_password():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $data = [
            'name' => 'Test User',
            'email' => 'weak@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}