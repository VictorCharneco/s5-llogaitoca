<?php

namespace Tests\Feature\Reservations;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class ReturnReservationTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_return_requires_authentication():void{
        $reservation = Reservation::factory()->create(['status' => 'ACTIVE']);
        $response = $this->postJson("/api/reservations/{$reservation->id}/return");
        $response->assertStatus(401);
    }

    public function test_user_cant_return_other_user_reservation():void{

        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $token = $user1->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create([
            'user_id' => $user2->id, 
            'status' => 'ACTIVE'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/reservations/{$reservation->id}/return");

        $response->assertStatus(403);
    }

    public function test_return_rejects_if_reservation_is_not_active(): void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'FINISHED',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/reservations/{$reservation->id}/return");

        $response->assertStatus(422);
    }

    public function test_user_can_return_own_active_reservation(): void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id, 
            'status' => 'ACTIVE'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/reservations/{$reservation->id}/return");

        $response->assertStatus(200);

    }

    public function test_return_404_if_reservation_not_found(): void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/reservations/1985/return");

        $response->assertStatus(404);

    }


}