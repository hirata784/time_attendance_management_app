<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 15.勤怠情報修正機能(管理者)
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
