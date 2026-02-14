<?php

namespace Tests\Feature\Reservations;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class DeleteReservationTest extends TestCase{
    use RefreshDatabase;

    public function test_delete_reservation_requires_authentication(): void{
        $reservation = Reservation::factory()->create();
        $response = $this->deleteJson("/api/reservations/{$reservation->id}");
        $response->assertStatus(401);
    }

    public function test_user_cant_delete_other_user_reservation(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $reservation = Reservation::factory()->create(['user_id' => $user2->id,]);
        $token = $user1->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_reservation(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create(['user_id' => $user->id,]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id,]);
    }

    public function test_admin_can_delete_any_reservation(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id,]);
    }

    public function test_delete_return_404_if_reservation_not_found():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/reservations/1985");

        $response->assertStatus(404);
    }

}