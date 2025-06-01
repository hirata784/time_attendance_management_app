<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use App\Models\Admin;
use Carbon\Carbon;
use Tests\TestCase;

class StaffTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 14.ユーザー情報取得機能(管理者)
    public function test_スタッフ一覧_ユーザー情報表示()
    {
        // テスト用ユーザーを作成(2人)
        $user = User::factory(2)->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/staff/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);

        for ($i = 0; $i <= 1; $i++) {
            // ユーザー情報表示確認
            $response->assertSee($user[$i]['name']); //名前
            $response->assertSee($user[$i]['email']); //メールアドレス
        }
    }

    public function test_スタッフ別勤怠一覧_勤怠情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
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
        // ログイン後、スタッフ一覧画面(管理者)へ移動
        $response = $this->get('/admin/staff/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // スタッフ別勤怠一覧画面へ移動
        $response = $this->get('/admin/attendance/staff/1');
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest['rest_start'] = new Carbon($rest['rest_start']);
        $rest['rest_finish'] = new Carbon($rest['rest_finish']);

        // 差分の分数を計算
        $rest_minute = $rest['rest_finish']->diffInMinutes($rest['rest_start']);
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
        $work['attendance_time'] = new Carbon($work['attendance_time']);
        $work['leaving_time'] = new Carbon($work['leaving_time']);
        $sum_minute = $work['leaving_time']->diffInMinutes($work['attendance_time']);
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
        $work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->Format('H:i');
        $work['leaving_time'] = \Carbon\Carbon::parse($work['leaving_time'])->Format('H:i');
        // 勤怠情報表示確認
        $response->assertSee($work['attendance_time']); //出勤
        $response->assertSee($work['leaving_time']); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_スタッフ別勤怠一覧_前月情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 前月
        $last_date = Carbon::now()->subMonth(1);
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($last_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
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
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、スタッフ一覧画面(管理者)へ移動
        $response = $this->get('/admin/staff/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // スタッフ別勤怠一覧画面へ移動
        $response = $this->get('/admin/attendance/staff/1');
        $response->assertStatus(200);

        // 前月ボタン押下
        $query = ['last-month' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/admin/attendance/staff/1/month', $query);
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest['rest_start'] = new Carbon($rest['rest_start']);
        $rest['rest_finish'] = new Carbon($rest['rest_finish']);

        // 差分の分数を計算
        $rest_minute = $rest['rest_finish']->diffInMinutes($rest['rest_start']);
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
        $work['attendance_time'] = new Carbon($work['attendance_time']);
        $work['leaving_time'] = new Carbon($work['leaving_time']);
        $sum_minute = $work['leaving_time']->diffInMinutes($work['attendance_time']);
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
        $work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->Format('H:i');
        $work['leaving_time'] = \Carbon\Carbon::parse($work['leaving_time'])->Format('H:i');
        // 勤怠情報表示確認
        $response->assertSee($work['attendance_time']); //出勤
        $response->assertSee($work['leaving_time']); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_スタッフ別勤怠一覧_翌月情報表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // 翌月
        $last_date = Carbon::now()->addMonth(1);
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($last_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($last_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
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
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
        // ログイン後、スタッフ一覧画面(管理者)へ移動
        $response = $this->get('/admin/staff/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // スタッフ別勤怠一覧画面へ移動
        $response = $this->get('/admin/attendance/staff/1');
        $response->assertStatus(200);

        // 翌月ボタン押下
        $query = ['next-month' => '', 'now_date' => $now_date];
        $response = $this->call('GET', '/admin/attendance/staff/1/month', $query);
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $rest['rest_start'] = new Carbon($rest['rest_start']);
        $rest['rest_finish'] = new Carbon($rest['rest_finish']);

        // 差分の分数を計算
        $rest_minute = $rest['rest_finish']->diffInMinutes($rest['rest_start']);
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
        $work['attendance_time'] = new Carbon($work['attendance_time']);
        $work['leaving_time'] = new Carbon($work['leaving_time']);
        $sum_minute = $work['leaving_time']->diffInMinutes($work['attendance_time']);
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
        $work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->Format('H:i');
        $work['leaving_time'] = \Carbon\Carbon::parse($work['leaving_time'])->Format('H:i');
        // 勤怠情報表示確認
        $response->assertSee($work['attendance_time']); //出勤
        $response->assertSee($work['leaving_time']); //退勤
        $response->assertSee($rest_time); //休憩
        $response->assertSee($sum_time); //合計
    }

    public function test_スタッフ別勤怠一覧_詳細画面遷移()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // テスト用管理者を作成
        $admin = Admin::factory([
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ])->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:00:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:00:00',
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:00:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:00:00',
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);
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
        // ログイン後、スタッフ一覧画面(管理者)へ移動
        $response = $this->get('/admin/staff/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // スタッフ別勤怠一覧画面へ移動
        $response = $this->get('/admin/attendance/staff/1');
        $response->assertStatus(200);

        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 各時間の時間形式変更
        $year = \Carbon\Carbon::parse($work['attendance_time'])->format('Y年');
        $month_day = \Carbon\Carbon::parse($work['attendance_time'])->format('n月j日');
        $work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->Format('H:i');
        $work['leaving_time'] = \Carbon\Carbon::parse($work['leaving_time'])->Format('H:i');
        $rest['rest_start'] = \Carbon\Carbon::parse($rest['rest_start'])->Format('H:i');
        $rest['rest_finish'] = \Carbon\Carbon::parse($rest['rest_finish'])->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($year); //年
        $response->assertSee($month_day); //月日
        $response->assertSee($work['attendance_time']); //出勤
        $response->assertSee($work['leaving_time']); //退勤
        $response->assertSee($rest['rest_start']); //休憩開始
        $response->assertSee($rest['rest_finish']); //休憩終了
    }
}
