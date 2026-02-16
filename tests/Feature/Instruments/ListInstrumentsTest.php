<?php

namespace Tests\Feature\Instruments;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class ListInstrumentsTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_list_instruments_requires_authentication():void{
        $response = $this->getJson('/api/instruments');
        $response->assertStatus(401);
    }

    public function test_user_can_list_instruments():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/instruments');

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'type', 'status', 'image_path', 'created_at', 'updated_at',],
            ],
        ]);
    }

    public function test_admin_can_list_instruments():void{

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/instruments');

        $response->assertStatus(200);
        
    }



}