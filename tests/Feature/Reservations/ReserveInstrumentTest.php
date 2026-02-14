<?php

namespace Tests\Feature\Reservations;

use App\Models\Instrument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

use function Symfony\Component\Clock\now;

class ReserveInstrumentTest extends TestCase{
    use RefreshDatabase;

    public function test_reserve_requires_authentication():void{
        $instrument = Instrument::factory()->create();
        $response = $this->postJson("/api/instruments/{$instrument->id}/reserve", []);
        $response->assertStatus(401);
    }

    public function test_reserve_rejects_admin():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();
        $data = ['start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(3)->format('Y-m-d'),];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reserve", $data);

        $response->assertStatus(403);
    }

    public function test_user_can_reserve_instrument():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'AVAILABLE']);
        $data = ['start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(3)->format('Y-m-d'),];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reserve", $data);

        $response->assertStatus(201);
    }

    public function test_reserve_validates_dates():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'AVAILABLE']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reserve", []);

        $response->assertStatus(422)->assertJsonValidationErrors(['start_date', 'end_date']);
    }
}