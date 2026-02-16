<?php

namespace Tests\Feature\Meetings;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class DeleteMeetingAdminTest extends TestCase{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_delete_meeting_requires_authentication():void{
        $meeting = Meeting::factory()->create();
        $response = $this->deleteJson("/api/meetings/{$meeting->id}");
        $response->assertStatus(401);
    }


    public function test_delete_meeting_requires_admin_role():void{

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/meetings/{$meeting->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_meeting():void {

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/meetings/{$meeting->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }

    public function test_delete_meeting_returns_404_if_not_found():void{

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/meetings/1985');

        $response->assertStatus(404);
    }





}