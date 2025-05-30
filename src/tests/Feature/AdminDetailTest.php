<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // 13.勤怠詳細情報取得・修正機能(管理者)
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
