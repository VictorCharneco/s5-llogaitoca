<?php

namespace Tests\Feature\Meetings;

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



}