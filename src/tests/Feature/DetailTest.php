<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Tests\TestCase;

class DetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 10.勤怠詳細情報取得機能(一般ユーザー)
    public function test_勤怠詳細_名前表示()
    {
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
        // ログインユーザーの名前表示を確認
        $response->assertSee($this->user['name']);
    }

    public function test_勤怠詳細_日付表示()
    {
        $this->withoutExceptionHandling();
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
        // 日付表示を確認
        $year = \Carbon\Carbon::parse($attendance_time)->format('Y年');
        $month_day = \Carbon\Carbon::parse($attendance_time)->format('n月j日');
        $response->assertSee($year);
        $response->assertSee($month_day);
    }

    public function test_勤怠詳細_出勤退勤表示()
    {
        $this->withoutExceptionHandling();
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
        // 出勤退勤表示を確認
        $attendance_time = \Carbon\Carbon::parse($attendance_time)->Format('H:i');
        $leaving_time = \Carbon\Carbon::parse($leaving_time)->Format('H:i');
        $response->assertSee($attendance_time);
        $response->assertSee($leaving_time);
    }

    public function test_勤怠詳細_休憩表示()
    {
        $this->withoutExceptionHandling();
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
        // 休憩時間表示を確認
        $rest_start = \Carbon\Carbon::parse($rest_start)->Format('H:i');
        $rest_finish = \Carbon\Carbon::parse($rest_finish)->Format('H:i');
        $response->assertSee($rest_start);
        $response->assertSee($rest_finish);
    }
}
