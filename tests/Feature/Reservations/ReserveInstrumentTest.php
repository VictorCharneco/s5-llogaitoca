<?php

namespace Tests\Feature\Reservations;

use App\Models\Reservation;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

use function Symfony\Component\Clock\now;

class ReserveInstrumentTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_reservations_requires_authentication():void{
        $instrument = Instrument::factory()->create();
        $response = $this->postJson("/api/instruments/{$instrument->id}/reservations", []);
        $response->assertStatus(401);
    }

    public function test_reservations_rejects_admin():void{
        
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create();
        $data = ['start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(3)->format('Y-m-d'),];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reservations", $data);

        $response->assertStatus(403);
    }

    public function test_user_can_reservations_instrument():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'AVAILABLE']);
        $data = ['start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(3)->format('Y-m-d'),];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reservations", $data);

        $response->assertStatus(201);
    }

    public function test_reservations_validates_dates():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'AVAILABLE']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reservations", []);

        $response->assertStatus(422)->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    public function test_reservations_returns_404_if_instrument_not_found():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/instruments/1985/reservations", [
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $response->assertStatus(404);
    }

    public function test_reserve_rejects_if_instrument_not_available(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'MAINTENANCE']); // cambiar aquí

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token ,
            'Accept'        => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reservations", [
            'start_date' => '2026-06-01',
            'end_date'   => '2026-06-10',
        ]);

        $response->assertStatus(422);
    }

    public function test_reserve_rejects_if_dates_overlap():void
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $instrument = Instrument::factory()->create(['status' => 'AVAILABLE']);

        Reservation::factory()->create([
            'instrument_id' => $instrument->id,
            'status'        => 'ACTIVE',
            'start_date'    => '2026-06-01',
            'end_date'      => '2026-06-15',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ])->postJson("/api/instruments/{$instrument->id}/reservations",[
            'start_date'    => '2026-06-10',
            'end_date'      => '2026-06-20',
        ]);

        $response->assertStatus(422);
    }

}