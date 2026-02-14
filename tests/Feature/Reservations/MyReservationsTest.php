<?php

namespace Tests\Feature\Reservations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;

class MyReservationsTest extends TestCase{
    use RefreshDatabase;

    public function test_my_reservation_requires_authentication():void{
        $response = $this->getJson('/api/reservations/my');
        $response->assertStatus(401);
    }

    public function test_user_can_list_my_reservations(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->count(2)->create(['user_id' => $user->id]);
        Reservation::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/reservations/my');

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_id', 'instrument_id', 'start_date', 'end_date', 'created_at', 'updated_at',],
            ],
        ])->assertJsonCount(2, 'data');
    }


}