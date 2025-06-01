<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use App\Models\Admin;
use Carbon\Carbon;
use Tests\TestCase;

class AdminListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 12.勤怠一覧情報取得機能(管理者)
    public function test_管理者勤怠一覧_情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // テスト用ユーザーを作成(2人)
        $user = User::factory(2)->create();
        // 退勤済ステータスが表示されるデータ作成(2人)
        $work[] = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        $work[] = Work::factory([
            'user_id' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '2',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('works', [
            'user_id' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '2',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);

        for ($i = 0; $i <= 1; $i++) {
            // 休憩合計時間
            // 休憩開始&終了時間を作成
            $rest[$i]['rest_start'] = new Carbon($rest[$i]['rest_start']);
            $rest[$i]['rest_finish'] = new Carbon($rest[$i]['rest_finish']);

            // 差分の分数を計算
            $rest_minute = $rest[$i]['rest_finish']->diffInMinutes($rest[$i]['rest_start']);
            // 分数から時間、分を計算
            $rest_hours = floor($rest_minute / 60);
            $rest_minutes = floor($rest_minute % 60);
            // 結果を表示
            if ($rest_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $rest_time[$i] = $rest_hours . ":0" . $rest_minutes;
            } else {
                $rest_time[$i] = $rest_hours . ":" . $rest_minutes;
            }

            // 勤務合計時間
            // 差分の分数を計算
            $work[$i]['attendance_time'] = new Carbon($work[$i]['attendance_time']);
            $work[$i]['leaving_time'] = new Carbon($work[$i]['leaving_time']);
            $sum_minute = $work[$i]['leaving_time']->diffInMinutes($work[$i]['attendance_time']);
            // 先程求めた休憩合計時間を引く
            $sum_minute = $sum_minute - $rest_minute;
            // 分数から時間、分を計算
            $sum_hours = floor($sum_minute / 60);
            $sum_minutes = floor($sum_minute % 60);
            // 結果を表示
            if ($sum_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $sum_time[$i] = $sum_hours . ":0" . $sum_minutes;
            } else {
                $sum_time[$i] = $sum_hours . ":" . $sum_minutes;
            }

            // 出勤退勤時間の時間形式変更
            $work[$i]['attendance_time'] = \Carbon\Carbon::parse($work[$i]['attendance_time'])->Format('H:i');
            $work[$i]['leaving_time'] = \Carbon\Carbon::parse($work[$i]['leaving_time'])->Format('H:i');
            // 勤怠情報表示確認
            $response->assertSee($user[$i]['name']); //ユーザー
            $response->assertSee($work[$i]['attendance_time']); //出勤
            $response->assertSee($work[$i]['leaving_time']); //退勤
            $response->assertSee($rest_time[$i]); //休憩
            $response->assertSee($sum_time[$i]); //合計
        }
    }

    public function test_管理者勤怠一覧_日付確認()
    {
        // データを取得
        $now_date = Carbon::now();
        // テスト用ユーザーを作成(2人)
        $user = User::factory(2)->create();
        // 退勤済ステータスが表示されるデータ作成(2人)
        $work[] = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        $work[] = Work::factory([
            'user_id' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '2',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('works', [
            'user_id' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '2',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);

        // 時間形式変更
        $now_date = \Carbon\Carbon::parse($now_date)->isoFormat('Y/MM/DD');
        // 現在日付表示確認
        $response->assertSee($now_date);
    }

    public function test_管理者勤怠一覧_前日情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 前日
        $last_date = Carbon::now()->subDay(1);
        // テスト用ユーザーを作成(2人)
        $user = User::factory(2)->create();
        // 退勤済ステータスが表示されるデータ作成(2人)
        $work[] = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($last_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($last_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($last_date, 0, 10) . ' 13:00:00',
        ])->create();
        $work[] = Work::factory([
            'user_id' => '2',
            'attendance_time' => substr($last_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 19:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '2',
            'rest_start' => substr($last_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($last_date, 0, 10) . ' 13:00:00',
        ])->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => substr($last_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 18:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => substr($last_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($last_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('works', [
            'user_id' => '2',
            'attendance_time' => substr($last_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '2',
            'rest_start' => substr($last_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($last_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);

        // 前日ボタン押下
        $query = ['last-day' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/admin/attendance/list/day', $query);
        $response->assertStatus(200);

        for ($i = 0; $i <= 1; $i++) {
            // 休憩合計時間
            // 休憩開始&終了時間を作成
            $rest[$i]['rest_start'] = new Carbon($rest[$i]['rest_start']);
            $rest[$i]['rest_finish'] = new Carbon($rest[$i]['rest_finish']);

            // 差分の分数を計算
            $rest_minute = $rest[$i]['rest_finish']->diffInMinutes($rest[$i]['rest_start']);
            // 分数から時間、分を計算
            $rest_hours = floor($rest_minute / 60);
            $rest_minutes = floor($rest_minute % 60);
            // 結果を表示
            if ($rest_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $rest_time[$i] = $rest_hours . ":0" . $rest_minutes;
            } else {
                $rest_time[$i] = $rest_hours . ":" . $rest_minutes;
            }

            // 勤務合計時間
            // 差分の分数を計算
            $work[$i]['attendance_time'] = new Carbon($work[$i]['attendance_time']);
            $work[$i]['leaving_time'] = new Carbon($work[$i]['leaving_time']);
            $sum_minute = $work[$i]['leaving_time']->diffInMinutes($work[$i]['attendance_time']);
            // 先程求めた休憩合計時間を引く
            $sum_minute = $sum_minute - $rest_minute;
            // 分数から時間、分を計算
            $sum_hours = floor($sum_minute / 60);
            $sum_minutes = floor($sum_minute % 60);
            // 結果を表示
            if ($sum_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $sum_time[$i] = $sum_hours . ":0" . $sum_minutes;
            } else {
                $sum_time[$i] = $sum_hours . ":" . $sum_minutes;
            }

            // 出勤退勤時間の時間形式変更
            $work[$i]['attendance_time'] = \Carbon\Carbon::parse($work[$i]['attendance_time'])->Format('H:i');
            $work[$i]['leaving_time'] = \Carbon\Carbon::parse($work[$i]['leaving_time'])->Format('H:i');
            // 勤怠情報表示確認
            $response->assertSee($user[$i]['name']); //ユーザー
            $response->assertSee($work[$i]['attendance_time']); //出勤
            $response->assertSee($work[$i]['leaving_time']); //退勤
            $response->assertSee($rest_time[$i]); //休憩
            $response->assertSee($sum_time[$i]); //合計
        }
    }

    public function test_管理者勤怠一覧_翌日情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 翌日
        $next_date = Carbon::now()->addDay(1);
        // テスト用ユーザーを作成(2人)
        $user = User::factory(2)->create();
        // 退勤済ステータスが表示されるデータ作成(2人)
        $work[] = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($next_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($next_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($next_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($next_date, 0, 10) . ' 13:00:00',
        ])->create();
        $work[] = Work::factory([
            'user_id' => '2',
            'attendance_time' => substr($next_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($next_date, 0, 10) . ' 19:00:00',
        ])->create();
        $rest[] = Rest::factory([
            'work_id' => '2',
            'rest_start' => substr($next_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($next_date, 0, 10) . ' 13:00:00',
        ])->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => substr($next_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($next_date, 0, 10) . ' 18:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => substr($next_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($next_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('works', [
            'user_id' => '2',
            'attendance_time' => substr($next_date, 0, 10) . ' 10:00:00',
            'leaving_time' => substr($next_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '2',
            'rest_start' => substr($next_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($next_date, 0, 10) . ' 13:00:00',
        ]);
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);

        // 翌日ボタン押下
        $query = ['next-day' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/admin/attendance/list/day', $query);
        $response->assertStatus(200);

        for ($i = 0; $i <= 1; $i++) {
            // 休憩合計時間
            // 休憩開始&終了時間を作成
            $rest[$i]['rest_start'] = new Carbon($rest[$i]['rest_start']);
            $rest[$i]['rest_finish'] = new Carbon($rest[$i]['rest_finish']);

            // 差分の分数を計算
            $rest_minute = $rest[$i]['rest_finish']->diffInMinutes($rest[$i]['rest_start']);
            // 分数から時間、分を計算
            $rest_hours = floor($rest_minute / 60);
            $rest_minutes = floor($rest_minute % 60);
            // 結果を表示
            if ($rest_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $rest_time[$i] = $rest_hours . ":0" . $rest_minutes;
            } else {
                $rest_time[$i] = $rest_hours . ":" . $rest_minutes;
            }

            // 勤務合計時間
            // 差分の分数を計算
            $work[$i]['attendance_time'] = new Carbon($work[$i]['attendance_time']);
            $work[$i]['leaving_time'] = new Carbon($work[$i]['leaving_time']);
            $sum_minute = $work[$i]['leaving_time']->diffInMinutes($work[$i]['attendance_time']);
            // 先程求めた休憩合計時間を引く
            $sum_minute = $sum_minute - $rest_minute;
            // 分数から時間、分を計算
            $sum_hours = floor($sum_minute / 60);
            $sum_minutes = floor($sum_minute % 60);
            // 結果を表示
            if ($sum_minutes < 10) {
                // 分が10未満の場合、0を１つ追加
                $sum_time[$i] = $sum_hours . ":0" . $sum_minutes;
            } else {
                $sum_time[$i] = $sum_hours . ":" . $sum_minutes;
            }

            // 出勤退勤時間の時間形式変更
            $work[$i]['attendance_time'] = \Carbon\Carbon::parse($work[$i]['attendance_time'])->Format('H:i');
            $work[$i]['leaving_time'] = \Carbon\Carbon::parse($work[$i]['leaving_time'])->Format('H:i');
            // 勤怠情報表示確認
            $response->assertSee($user[$i]['name']); //ユーザー
            $response->assertSee($work[$i]['attendance_time']); //出勤
            $response->assertSee($work[$i]['leaving_time']); //退勤
            $response->assertSee($rest_time[$i]); //休憩
            $response->assertSee($sum_time[$i]); //合計
        }
    }
}
