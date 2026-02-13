<?php

namespace Tests\Feature\Instruments;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class CreateInstrumentAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_create_instrument_requires_authentication():void{
        $response = $this->postJson('/api/instruments', []);
        $response->assertStatus(401);
    }

    public function test_create_instrument_requires_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $data = [
            'name' => 'Trompeta',
            'description' => 'Instrument de vent',
            'type' => 'WIND',
            'status' => 'AVAILABLE',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/instruments', $data);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_instrument():void {
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $data = [
            'name' => 'Trompeta',
            'description' => 'Instrument de vent',
            'type' => 'WIND',
            'status' => 'AVAILABLE',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/instruments', $data);

        $response->assertStatus(201)->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'type', 'status', 'image_path', 'created_at', 'updated_at',],
        ])
        ->assertJsonPath('data.name', 'Trompeta')
        ->assertJsonPath('data.type', 'WIND')
        ->assertJsonPath('data.status', 'AVAILABLE');

        $this->assertDatabaseHas('instruments',[
            'name' => 'Trompeta',
            'type' => 'WIND',
            'status' => 'AVAILABLE',
        ]);

    }


    public function test_create_instrument_validates_required_fields():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/instruments', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'description', 'type', 'status']);
    }

    public function test_create_instrument_rejects_invalid_type_and_status():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $data = [
            'name' => 'Qualsevol cosa',
            'description' => 'Descripció',
            'type' => 'WRONGTYPE',
            'status' => 'WRONGSTATUS',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/instruments', $data);

        $response->assertStatus(422)->assertJsonValidationErrors(['type', 'status']);

    }



}