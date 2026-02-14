<?php

namespace Tests\Feature\Reservations;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class ReservationsIndexAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_reservations_index_requires_authentication():void{
        $response = $this->getJson('/api/reservations');
        $response->assertStatus(401);
    }

    public function test_reservations_index_requires_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/reservations');

        $response->assertStatus(403);
    }

    public function test_admin_can_list_reservations():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        Reservation::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/reservations');

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
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                        'updated_at',
                    ],
                    'instrument' => [
                        'id',
                        'name',
                        'description',
                        'type',
                        'status',
                        'image_path',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ])->assertJsonCount(3, 'data');
    }




}