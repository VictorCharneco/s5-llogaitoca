<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase{

    use RefreshDatabase;

    public function test_logout_requires_authentication():void{
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }


    public function test_logout_revokes_token():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');


        $user = User::factory()->create(['role' => 'user',]);
        $token = $user->createToken('api-token')->accessToken;

        $logout = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');
        $logout->assertStatus(200);

        $this->app['auth']->forgetGuards();

        $me = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/me');
        $me->assertStatus(401);

    }

}