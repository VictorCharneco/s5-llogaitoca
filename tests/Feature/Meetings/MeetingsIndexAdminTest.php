<?php

namespace Tests\Feature\Meetings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class MeetingsIndexAdminTest extends TestCase{
    use RefreshDatabase;

    public function test_meetings_index_requires_authentication():void{
        $response = $this->getJson('/api/meetings');
        $response->assertStatus(401);
    }

    public function test_meetings_index_requires_admin_role(): void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/meetings');

        $response->assertStatus(403);
    }




}