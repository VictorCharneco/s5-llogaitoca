<?php

namespace Tests\Feature\Meetings;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;


class JoinMeetingTest extends TestCase{
    use RefreshDatabase;

    public function test_join_requires_authentication():void {
        $response = $this->postJson('/api/meetings/1/join', []);
        $response->assertStatus(401);
    }

    public function test_join_returns_404_if_meeting_not_found():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/meetings/1/join', []);

        $response->assertStatus(404);
    }





}