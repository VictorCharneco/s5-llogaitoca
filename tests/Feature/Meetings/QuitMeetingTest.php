<?php

namespace Tests\Feature\Meetings;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class QuitMeetingTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_quit_meeting_requires_authentication():void{
        $meeting = Meeting::factory()->create();
        $response = $this->postJson("/api/meetings/{$meeting->id}/quit");
        $response->assertStatus(401);
    }

    public function test_quit_returns_404_if_meeting_not_found():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings/1985/quit');

        $response->assertStatus(404);
    }

    public function test_user_cant_quit_if_not_joined():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/quit");

        $response->assertStatus(422);
    }

    public function test_user_can_quit_meeting():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();
        $meeting->users()->attach($user->id);

        $this->assertDatabaseHas('meeting_user', [
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/meetings/{$meeting->id}/quit");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('meeting_user', [
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);
    }









}