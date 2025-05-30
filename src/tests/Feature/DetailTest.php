<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 10.勤怠詳細情報取得機能(一般ユーザー)
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
