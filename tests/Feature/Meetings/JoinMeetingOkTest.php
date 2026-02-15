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
        app(ClientRepository::class)->createPersonalAccessGrantClient('Personal Access Client Test');

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


}