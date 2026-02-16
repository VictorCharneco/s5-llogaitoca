<?php


namespace Tests\Feature\Instruments;

use App\Models\Instrument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class ShowInstrumentsTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_show_instrument_requires_authentication(): void{
        $instrument = Instrument::factory()->create();
        $response = $this->getJson("/api/instruments/{$instrument->id}");
        $response->assertStatus(401);
    }

    public function test_user_can_view_instruments():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $instrument = Instrument::factory()->create([
            'name' => 'Trompeta',
            'type' => 'WIND',
            'status' => 'AVAILABLE',
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/instruments/{$instrument->id}");

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'type', 'status', 'image_path', 'created_at', 'updated_at',],
        ])
        ->assertJsonPath('data.name', 'Trompeta')
        ->assertJsonPath('data.status', 'AVAILABLE');
    }

    public function test_returns_404_if_instrument_not_found():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/instruments/1985");

        $response->assertStatus(404);
    }


}