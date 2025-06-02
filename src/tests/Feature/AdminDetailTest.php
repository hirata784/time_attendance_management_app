<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use App\Models\Admin;
use Carbon\Carbon;
use Tests\TestCase;

class AdminDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 13.勤怠詳細情報取得・修正機能(管理者)
    public function test_管理者勤怠詳細_内容表示()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
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

        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
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
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($year); //年
        $response->assertSee($month_day); //月日
        $response->assertSee($work['attendance_time']); //出勤
        $response->assertSee($work['leaving_time']); //退勤
        $response->assertSee($rest['rest_start']); //休憩開始
        $response->assertSee($rest['rest_finish']); //休憩終了
    }

    public function test_管理者勤怠詳細_出勤退勤時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
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

        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 出勤時間を退勤時間より後に設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '19:00',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('attendance_time');

        $errors = session('errors');
        $this->assertEquals('出勤時間もしくは退勤時間が不適切な値です', $errors->first('attendance_time'));
    }

    public function test_管理者勤怠詳細_休憩開始時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
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

        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 休憩開始時間を退勤時間より後に設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['19:00', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('rest_start.0');

        $errors = session('errors');
        $this->assertEquals('休憩時間が勤務時間外です', $errors->first('rest_start.0'));
    }

    public function test_管理者勤怠詳細_休憩終了時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
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

        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 休憩終了時間を退勤時間より後に設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['19:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('rest_finish.0');

        $errors = session('errors');
        $this->assertEquals('休憩時間が勤務時間外です', $errors->first('rest_finish.0'));
    }

    public function test_管理者勤怠詳細_備考欄未入力エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/admin/attendance';
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
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

        // ログイン後、勤怠一覧画面(管理者)へ移動
        $response = $this->get('/admin/attendance/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 備考欄を未入力に設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => null,
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('remarks');

        $errors = session('errors');
        $this->assertEquals('備考を記入してください', $errors->first('remarks'));
    }
}
