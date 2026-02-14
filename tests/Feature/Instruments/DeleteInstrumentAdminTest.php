<?php

namespace Tests\Feature\Instruments;

use App\Models\Instrument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class DeleteInstrumentAdminTest extends TestCase{

    use RefreshDatabase;

    public function test_delete_instrument_requires_authentication():void{
        $instrument = Instrument::factory()->create();
        $response = $this->deleteJson("/api/instruments/{$instrument->id}");
        $response->assertStatus(401);
    }

    public function test_delete_instrument_requires_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/instruments/{$instrument->id}");

        $response->assertStatus(403);
    }


    public function test_admin_can_delete_instruments():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Application' => 'application/json',
        ])->deleteJson("/api/instruments/{$instrument->id}");

        $response->assertStatus(200);
    }

    
    public function test_delete_returns_404_if_instrument_not_found():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/instruments/1985");

        $response->assertStatus(404);
    }
}