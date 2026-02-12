<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
}