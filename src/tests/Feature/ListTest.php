<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Tests\TestCase;

class ListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 9.勤怠一覧情報取得機能(一般ユーザー)
    public function test_勤怠一覧_情報表示()
    {
        // データを取得
        $now_date = Carbon::now()->isoFormat('MM/DD(ddd)'); //日付
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 休憩開始時間を取得
        $rest_start = Carbon::now()->addMinute(5);
        // 休憩終了時間を取得
        $rest_finish = Carbon::now()->addMinute(10);
        // 退勤日時を取得
        $leaving_time = Carbon::now()->addMinute(20);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
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
        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest_start = new Carbon($rest_start);
        $rest_finish = new Carbon($rest_finish);
        // 差分の分数を計算
        $rest_minute = $rest_finish->diffInMinutes($rest_start);
        // 分数から時間、分を計算
        $rest_hours = floor($rest_minute / 60);
        $rest_minutes = floor($rest_minute % 60);
        // 結果を表示
        if ($rest_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $rest_time = $rest_hours . ":0" . $rest_minutes;
        } else {
            $rest_time = $rest_hours . ":" . $rest_minutes;
        }

        // 勤務合計時間
        // 差分の分数を計算
        $sum_minute = $leaving_time->diffInMinutes($attendance_time);
        // 先程求めた休憩合計時間を引く
        $sum_minute = $sum_minute - $rest_minute;
        // 分数から時間、分を計算
        $sum_hours = floor($sum_minute / 60);
        $sum_minutes = floor($sum_minute % 60);
        // 結果を表示
        if ($sum_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $sum_time = $sum_hours . ":0" . $sum_minutes;
        } else {
            $sum_time = $sum_hours . ":" . $sum_minutes;
        }

        // 出勤退勤時間の時間形式変更
        $attendance_time = \Carbon\Carbon::parse($attendance_time)->Format('H:i');
        $leaving_time = \Carbon\Carbon::parse($leaving_time)->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($now_date); //日付
        $response->assertSee($attendance_time); //出勤
        $response->assertSee($leaving_time); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_勤怠一覧_現在月表示()
    {
        // データを取得
        $now_date = Carbon::now()->isoFormat('Y/MM'); //日付

        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ログイン後、勤怠一覧画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        // 現在の年月を確認
        $response->assertSee($now_date);
    }

    public function test_勤怠一覧_前月情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 前月の出勤日時を取得
        $attendance_time = Carbon::now()->subMonth(1);
        // 前月の休憩開始時間を取得
        $rest_start = Carbon::now()->addMinute(5)->subMonth(1);
        // 前月の休憩終了時間を取得
        $rest_finish = Carbon::now()->addMinute(10)->subMonth(1);
        // 前月の退勤日時を取得
        $leaving_time = Carbon::now()->addMinute(20)->subMonth(1);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
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
        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 前月ボタン押下
        $query = ['last-month' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/attendance/list/month', $query);
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest_start = new Carbon($rest_start);
        $rest_finish = new Carbon($rest_finish);
        // 差分の分数を計算
        $rest_minute = $rest_finish->diffInMinutes($rest_start);
        // 分数から時間、分を計算
        $rest_hours = floor($rest_minute / 60);
        $rest_minutes = floor($rest_minute % 60);
        // 結果を表示
        if ($rest_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $rest_time = $rest_hours . ":0" . $rest_minutes;
        } else {
            $rest_time = $rest_hours . ":" . $rest_minutes;
        }

        // 勤務合計時間
        // 差分の分数を計算
        $sum_minute = $leaving_time->diffInMinutes($attendance_time);
        // 先程求めた休憩合計時間を引く
        $sum_minute = $sum_minute - $rest_minute;
        // 分数から時間、分を計算
        $sum_hours = floor($sum_minute / 60);
        $sum_minutes = floor($sum_minute % 60);
        // 結果を表示
        if ($sum_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $sum_time = $sum_hours . ":0" . $sum_minutes;
        } else {
            $sum_time = $sum_hours . ":" . $sum_minutes;
        }

        // 時間形式変更
        $last_date = \Carbon\Carbon::parse($now_date)->subMonth(1)->isoFormat('MM/DD(ddd)');
        $attendance_time = \Carbon\Carbon::parse($attendance_time)->Format('H:i');
        $leaving_time = \Carbon\Carbon::parse($leaving_time)->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($last_date); //日付
        $response->assertSee($attendance_time); //出勤
        $response->assertSee($leaving_time); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_勤怠一覧_翌月情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 前月の出勤日時を取得
        $attendance_time = Carbon::now()->addMonth(1);
        // 前月の休憩開始時間を取得
        $rest_start = Carbon::now()->addMinute(5)->addMonth(1);
        // 前月の休憩終了時間を取得
        $rest_finish = Carbon::now()->addMinute(10)->addMonth(1);
        // 前月の退勤日時を取得
        $leaving_time = Carbon::now()->addMinute(20)->addMonth(1);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
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
        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 前月ボタン押下
        $query = ['next-month' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/attendance/list/month', $query);
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest_start = new Carbon($rest_start);
        $rest_finish = new Carbon($rest_finish);
        // 差分の分数を計算
        $rest_minute = $rest_finish->diffInMinutes($rest_start);
        // 分数から時間、分を計算
        $rest_hours = floor($rest_minute / 60);
        $rest_minutes = floor($rest_minute % 60);
        // 結果を表示
        if ($rest_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $rest_time = $rest_hours . ":0" . $rest_minutes;
        } else {
            $rest_time = $rest_hours . ":" . $rest_minutes;
        }

        // 勤務合計時間
        // 差分の分数を計算
        $sum_minute = $leaving_time->diffInMinutes($attendance_time);
        // 先程求めた休憩合計時間を引く
        $sum_minute = $sum_minute - $rest_minute;
        // 分数から時間、分を計算
        $sum_hours = floor($sum_minute / 60);
        $sum_minutes = floor($sum_minute % 60);
        // 結果を表示
        if ($sum_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $sum_time = $sum_hours . ":0" . $sum_minutes;
        } else {
            $sum_time = $sum_hours . ":" . $sum_minutes;
        }

        // 時間形式変更
        $last_date = \Carbon\Carbon::parse($now_date)->addMonth(1)->isoFormat('MM/DD(ddd)');
        $attendance_time = \Carbon\Carbon::parse($attendance_time)->Format('H:i');
        $leaving_time = \Carbon\Carbon::parse($leaving_time)->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($last_date); //日付
        $response->assertSee($attendance_time); //出勤
        $response->assertSee($leaving_time); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_勤怠一覧_詳細画面遷移()
    {
        // データを取得
        $now_date = Carbon::now()->isoFormat('MM/DD(ddd)'); //日付
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

        // 各時間の時間形式変更
        $attendance_time = \Carbon\Carbon::parse($attendance_time)->Format('H:i');
        $leaving_time = \Carbon\Carbon::parse($leaving_time)->Format('H:i');
        $year = \Carbon\Carbon::parse($attendance_time)->format('Y年');
        $month_day = \Carbon\Carbon::parse($attendance_time)->format('n月j日');
        $rest_start = \Carbon\Carbon::parse($rest_start)->Format('H:i');
        $rest_finish = \Carbon\Carbon::parse($rest_finish)->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($year);
        $response->assertSee($month_day);
        $response->assertSee($attendance_time);
        $response->assertSee($leaving_time);
        $response->assertSee($rest_start);
        $response->assertSee($rest_finish);
    }
}
