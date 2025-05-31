<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Tests\TestCase;

class CorrectionDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 11.勤怠詳細情報修正機能(一般ユーザー)
    public function test_勤怠詳細_出勤退勤エラー()
    {
        // $this->withoutExceptionHandling();
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 休憩開始時間を取得
        $rest_start = Carbon::now()->addMinute(5);
        // 休憩終了時間を取得
        $rest_finish = Carbon::now()->addMinute(10);
        // 退勤日時を取得
        $leaving_time = Carbon::now()->addMinute(20);
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';

        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $leaving_time,
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
            'rest_start' => $rest_start,
            'rest_finish' => $rest_finish,
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $leaving_time,
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => $rest_start,
            'rest_finish' => $rest_finish,
        ]);

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 出勤時間を退勤時間より後に設定し、[修正]押下
        $response = $this->post('/attendance/1/store', [
            'attendance_time' => '20:00',
            'leaving_time' => '18:00',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('attendance_time');
        $errors = session('errors');
        $this->assertEquals('出勤時間もしくは退勤時間が不適切な値です', $errors->first('attendance_time'));
    }
}