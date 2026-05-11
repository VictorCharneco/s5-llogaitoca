<?php

namespace Tests\Feature\Meetings;

use App\Models\Reservation;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class JoinMeetingTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_users_requires_authentication():void {
        $response = $this->postJson('/api/meetings/1/users', []);
        $response->assertStatus(401);
    }

    public function test_users_returns_404_if_meeting_not_found():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings/1/users', []);

        $response->assertStatus(404);
    }

    public function test_users_returns_404_if_meeting_is_not_active():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create([
            'status' => 'FINISHED',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/users");

        $response->assertStatus(404);
    }

    public function test_user_can_users_meeting(): void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $meeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/users");

        $response->assertStatus(200);

        $this->assertDatabaseHas('meeting_user', [
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_users_rejects_if_user_already_in():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $meeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);

        $meeting->users()->attach($user->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/users");

        $response->assertStatus(422);
    }

    public function test_users_rejects_if_meeting_is_full():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $meeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);

        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $user3 = User::factory()->create(['role' => 'user']);
        $user4 = User::factory()->create(['role' => 'user']);

        $meeting->users()->attach([$user1->id,$user2->id,$user3->id,$user4->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/users");

        $response->assertStatus(422);
    }


    public function test_users_rejects_if_user_has_no_active_reservation():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'FINISHED',
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $meeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/users");

        $response->assertStatus(422);

    }



    public function test_users_rejects_if_user_has_time_overlap():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        Reservation::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'start_date' => '2026-03-20',
            'end_date' => '2026-03-30',
        ]);

        $actualMeeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '18:00',
            'end_time' => '20:00',
        ]);

        $actualMeeting->users()->attach($user->id);

        $newMeeting = Meeting::factory()->create([
            'status' => 'ACTIVE',
            'day' => '2026-03-20',
            'start_time' => '19:00',
            'end_time' => '21:00',
        ]);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$newMeeting->id}/users");

        $response->assertStatus(422);


    }

}