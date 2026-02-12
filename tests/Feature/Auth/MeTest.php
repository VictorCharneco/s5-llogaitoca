<?php

namespace Tests\Feature\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeTest extends TestCase{
    use RefreshDatabase;

    public function test_me_require_to_be_authenticated(): void{
        $response = $this->getJSon('/api/me');
        $response->assertStatus(401);
    }




}