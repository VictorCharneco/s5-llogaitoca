<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class LoginInvalidPasswordTest extends TestCase{
    use RefreshDatabase;

    public function test_login_rejected_with_wrong_password(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        User::factory()->create([
            'email' => 'test@example',
            'password' => Hash::make('valido123'),
            'role' => 'user',
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'erroneo321',
        ];

        $response = $this->postJson('api/login', $data);
        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }    

}