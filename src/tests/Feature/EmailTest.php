<?php

namespace Tests\Feature;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Validator;
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
    }
}
