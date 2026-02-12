<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LogoutTest extends TestCase{

    use RefreshDatabase;

    public function test_logout_requires_authentication():void{
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }



}