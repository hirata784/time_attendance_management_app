<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use Carbon\Carbon;
use Tests\TestCase;

class LeavingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 8.退勤機能
    public function test_勤怠登録_退勤ボタン機能()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 退勤時間を取得(出勤時間の30分後に設定)
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 出勤中ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => null,
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => null,
        ]);
        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 退勤の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 退勤ボタンの表示を確認
        $response->assertSee('退勤');
        // 退勤ボタンを押下するため、workキーを持たせる
        $response = $this->post('/attendance/update_work', ['work' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('退勤済');
    }

    public function test_勤怠登録_退勤時刻確認()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 退勤時間を取得(出勤時間の30分後に設定)
        $leaving_time = Carbon::now()->addMinute(30)->format('H:i');
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 出勤の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('勤務外');
        // 出勤ボタンの表示を確認
        $response->assertSee('出勤');
        // 退勤ボタンを押下するため、workキーを持たせる
        $response = $this->post('/attendance/store');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 退勤の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 退勤ボタンの表示を確認
        $response->assertSee('退勤');
        // 退勤ボタンを押下するため、workキーを持たせる
        $response = $this->post('/attendance/update_work', ['work' => '', 'now_time' => $leaving_time]);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');

        // 勤怠一覧画面を表示
        $response = $this->get('/attendance/list');
        // 退勤の時間表示を確認
        $response->assertSee($leaving_time);
    }
}
