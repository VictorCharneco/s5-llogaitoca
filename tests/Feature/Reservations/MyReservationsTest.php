<?php

namespace Tests\Feature\Reservations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;

class MyReservationsTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_my_reservation_requires_authentication():void{
        $response = $this->getJson('/api/reservations/my');
        $response->assertStatus(401);
    }

    public function test_user_can_list_my_reservations(): void{

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
                '*' => [
                    'id',
                    'user_id',
                    'instrument_id',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])->assertJsonCount(2, 'data');
    }


    public function test_user_can_filter_my_reservations_by_status():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
        ]);

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'FINISHED',
        ]);

        $user2 = User::factory()->create(['role' => 'user']);
        Reservation::factory()->create([
            'user_id' => $user2->id,
            'status' => 'ACTIVE',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/reservations/my?status=ACTIVE');

        $response->assertStatus(200)
        ->assertJsonCount(1, 'data')->assertJsonPath('data.0.status', 'ACTIVE');
    }
}