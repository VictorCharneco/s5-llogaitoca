<?php

namespace Tests\Feature\Reservations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyReservationsTest extends TestCase{
    use RefreshDatabase;

    public function test_my_reservation_requires_authentication():void{
        $response = $this->getJson('/api/reservations/my');
        $response->assertStatus(401);
    }


}