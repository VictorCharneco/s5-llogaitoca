<?php

namespace Tests\Feature\Meetings;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class CreateMeetingsTest extends TestCase{
    use RefreshDatabase;

    public function test_create_meeting_requires_authentication():void{
        $response = $this->postJson('/api/meetings', []);
        $response->assertStatus(401);
    }

    public function test_create_meeting_rejects_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create();
        $data = [
            'reservation_id' => $reservation->id,
            'room' => 'DYLAN',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings', $data);

        $response->assertStatus(403);

    }

    public function test_user_can_create_meeting():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-30',
        ]);
        $data = [
            'reservation_id' => $reservation->id,
            'room' => 'DYLAN',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('meetings', [
            'reservation_id' => $reservation->id,
            'room' => 'DYLAN',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);
    }

    public function test_create_meeting_validates_required_fields():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings', []);

        $response->assertStatus(422);
    }







}
