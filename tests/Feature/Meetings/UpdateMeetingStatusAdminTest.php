<?php

namespace Tests\Feature\Meetings;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class UpdateMeetingStatusAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_update_status_requires_authentication():void{
        $meeting = Meeting::factory()->create();
        $response = $this->patchJson("/api/meetings/{$meeting->id}/status", [
            'status' => 'CANCELLED',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_status_requires_admin_role():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson("/api/meetings/{$meeting->id}/status", [
            'status' => 'CANCELLED',
        ]);

        $response->assertStatus(403);
    }


    public function test_admin_can_update_meeting_status():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create(['status' => 'ACTIVE']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson("/api/meetings/{$meeting->id}/status", [
            'status' => 'CANCELLED',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'data' => ['id', 'status'],
        ])->assertJsonPath('data.status', 'CANCELLED');

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'status' => 'CANCELLED',
        ]);
    }

    public function test_update_status_validates_required_fields():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson("/api/meetings/{$meeting->id}/status", []);

        $response->assertStatus(422);
    }


    public function test_update_status_rejects_invalid_status():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;
        $meeting = Meeting::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson("/api/meetings/{$meeting->id}/status", [
            'status' => 'WRONG_STATUS',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_status_returns_404_if_meeting_not_found():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson('/api/meetings/1985/status', [
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(404);
    }

}