<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 2.ログイン認証機能(一般ユーザー)
    // 3.ログイン認証機能(管理者)
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
