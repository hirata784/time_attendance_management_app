<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 4.日時取得機能
    public function test_勤怠登録_日時情報取得()
    {
        // 現在の日時を取得
        $now_date = Carbon::now()->isoFormat('Y年M月D日(ddd)'); //日付
        $now_time = Carbon::now()->format('H:i'); //時間

        // テスト用ユーザーを作成
        $user = User::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('users', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);

        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されている日時を取得
        $response->assertSee($now_date); //日付
        $response->assertSee($now_time); //時間
    }
}
