<?php

namespace Tests\Feature\Instruments;

use App\Models\Instrument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class UpdateInstrumentAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_update_instrument_requires_authentication(): void{
        $instrument = Instrument::factory()->create();
        $response = $this->putJson("/api/instruments/{$instrument->id}", []);
        $response->assertStatus(401);
    }

    public function test_update_instrument_requires_admin_role(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();

        $data = [
            'name' => 'Nom',
            'description' => 'Descripció',
            'type' => 'STRING',
            'status' => 'MAINTENANCE',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/instruments/{$instrument->id}", $data);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_instrument():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create([
            'name' => 'Antic nom',
            'type' => 'WIND',
            'status' => 'AVAILABLE',
        ]);

        $data = [
            'name' => 'Nou nom',
            'description' => 'Nova descripció',
            'type' => 'STRING',
            'status' => 'MAINTENANCE',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/instruments/{$instrument->id}", $data);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'type', 'status', 'image_path', 'created_at', 'updated_at',],
        ])
        ->assertJsonPath('data.name', 'Nou nom')
        ->assertJsonPath('data.type', 'STRING')
        ->assertJsonPath('data.status', 'MAINTENANCE');

        $this->assertDatabaseHas('instruments',[
            'id' => $instrument->id,
            'name' => 'Nou nom',
            'type' => 'STRING',
            'status' => 'MAINTENANCE',
        ]);
    }

    public function test_update_returns_404_instrument_not_found():void {
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $data = [
            'name' => 'Nou nom',
            'description' => 'Nova descripció',
            'type' => 'STRING',
            'status' => 'MAINTENANCE',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/instruments/1985", $data);

        $response->assertStatus(404);
    }

    public function test_update_validates_required_fields(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/instruments/{$instrument->id}", []);

        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'description', 'type', 'status']);
    }

    public function test_update_rejects_invalid_type_and_status():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Personal Access Client Test');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();

        $data = [
            'name' => 'Nom',
            'description' => 'Descripció',
            'type' => 'WRONGTYPE',
            'status' => 'WRONGSTATUS',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/instruments/{$instrument->id}", $data);

        $response->assertStatus(422)->assertJsonValidationErrors(['type', 'status']);
    }

}