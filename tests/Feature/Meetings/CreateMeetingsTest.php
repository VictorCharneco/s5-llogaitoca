<?php

namespace Tests\Feature\Meetings;

use App\Models\Meeting;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class CreateMeetingsTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_create_meeting_requires_authentication():void{
        $response = $this->postJson('/api/meetings', []);
        $response->assertStatus(401);
    }

    public function test_create_meeting_rejects_admin_role():void{

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

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings', []);

        $response->assertStatus(422);
    }

    public function test_create_meeting_rejects_if_reservation_is_not_mine():void{

        $user = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $reservation = Reservation::factory()->create([
            'user_id' => $user2->id,
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

        $response->assertStatus(422);
    }

    public function test_create_meeting_rejects_if_reservation_is_not_active():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'FINISHED',
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

        $response->assertStatus(422);
    }

    public function test_create_meeting_rejects_if_day_is_outside_rental_period():void{

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
            'day' => '2026-10-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings', $data);

        $response->assertStatus(422);
    }

    public function test_create_meeting_rejects_if_room_has_overlap():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-30',
        ]);

        Meeting::factory()->create([
            'status' => 'ACTIVE',
            'room' => 'DYLAN',
            'day' => '2026-03-20',
            'start_time' => '19:00',
            'end_time' => '21:00',
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

        $response->assertStatus(422);

    }

    public function test_create_meeting_rejects_if_user_has_overlap():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-30',
        ]);

        $actualMeeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'room' => 'ARMSTRONG',
            'day' => '2026-03-20',
            'start_time' => '19:00',
            'end_time' => '21:00',
        ]);
        $actualMeeting->users()->attach($user->id);

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

        $response->assertStatus(422);
    }

}
