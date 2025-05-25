<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 9.勤怠一覧情報取得機能(一般ユーザー)
    // 12.勤怠一覧情報取得機能(管理者)
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
