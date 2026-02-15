<?php

namespace Tests\Feature\Meetings;

use App\Models\Reservation;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class JoinMeetingOkTest extends TestCase{
    use RefreshDatabase;

    public function test_user_can_join_meeting(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])->postJson("/api/meetings/{$meeting->id}/join");

        $response->assertStatus(200);

        $this->assertDatabaseHas('meeting_user', [
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_join_rejects_if_user_already_in():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])->postJson("/api/meetings/{$meeting->id}/join");

        $response->assertStatus(422);
    }

    public function test_join_rejects_if_meeting_is_full():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])->postJson("/api/meetings/{$meeting->id}/join");

        $response->assertStatus(422);
    }


    public function test_join_rejects_if_user_has_no_active_reservation():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])->postJson("/api/meetings/{$meeting->id}/join");

        $response->assertStatus(422);

    }



    public function test_join_rejects_if_user_has_time_overlap():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

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
        ])->postJson("/api/meetings/{$newMeeting->id}/join");

        $response->assertStatus(422);


    }
}