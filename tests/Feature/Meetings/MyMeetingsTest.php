<?php

namespace Tests\Feature\Meetings;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class MyMeetingsTest extends TestCase{
    use RefreshDatabase;


    public function test_my_meetings_requires_authentication():void{
        $response = $this->getJSon('/api/meetings/my');
        $response->assertStatus(401);
    }


    public function test_user_can_list_my_meetings():void{
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');

        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('api-token')->accessToken;
        $myMeeting = Meeting::factory()->create();
        $myMeeting->users()->attach($user->id);

        Meeting::factory()->create();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/meetings/my');

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'reservation_id',
                    'room',
                    'day',
                    'start_time',
                    'end_time',
                    'status',
                    'created_at',
                    'updated_at',
                    'users_count',
                    'reservation',
                    'users',
                ],
            ],
        ])->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $myMeeting->id);

    }



}