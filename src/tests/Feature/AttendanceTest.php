<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 6.出勤機能
    public function test_勤怠登録_出勤ボタン機能()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('勤務外');
        // 出勤ボタンの表示を確認
        $response->assertSee('<button class="btn-black">出勤</button>', $escaped = false);
        // 出勤処理を行う
        $response = $this->post('/attendance/store');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');

        // 出勤後、勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
    }

    public function test_勤怠登録_出勤一日一回のみ()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 退勤日時を取得
        $attendance_time = Carbon::now()->addMinute(10);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $attendance_time,
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $attendance_time,
        ]);

        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('退勤済');
        // 出勤ボタンの非表示を確認(別の出勤を拾ってしまってる)
        $response->assertDontSee('<button class="btn-black">出勤</button>', $escaped = false);
    }

    public function test_勤怠登録_出勤時刻記録()
    {
        // 出勤日時を取得
        $now_date = Carbon::now()->isoFormat('MM/DD(ddd)'); //日付
        $now_time = Carbon::now()->format('H:i'); //時間
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('勤務外');
        // 出勤ボタンの表示を確認
        $response->assertSee('<button class="btn-black">出勤</button>', $escaped = false);
        // 出勤処理を行う
        $response = $this->post('/attendance/store');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');

        // 出勤処理後、一覧画面にて出勤日時を確認
        $response = $this->get('/attendance/list');
        $response->assertSee($now_date); //日付
        $response->assertSee($now_time); //時間
    }
}
