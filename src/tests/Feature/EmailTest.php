<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use GuzzleHttp\Client;
use Tests\TestCase;

class EmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 16.メール認証機能
    public function test_メール認証誘導_メール認証サイト表示()
    {
        // 会員登録をする
        $response = $this->post('/register', [
            'name' => "テストユーザ",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);
        // メール認証誘導画面へ移動
        $response = $this->get('/email/verify');
        $response->assertStatus(200);

        //client
        $client = new Client;
        //認証はこちらからを押下
        $url = "http://host.docker.internal:8025/";
        $response = $client->get($url);
        //メール認証サイトを表示
        $obj = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
