<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
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
    public function test_勤怠詳細_出勤退勤時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
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

    public function test_勤怠詳細_休憩開始時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
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

    public function test_勤怠詳細_休憩終了時間エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
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

    public function test_勤怠詳細_備考欄未入力エラー()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
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

    public function test_勤怠詳細_修正申請処理()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 正しい値を設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        // 勤怠詳細画面へリダイレクト
        $response->assertRedirect('/attendance/1');
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 管理者ユーザーで承認画面と申請一覧画面を確認する
        // 一般ユーザーをログアウトする
        $response = $this->post('/logout');
        //ログアウトしていることを確認
        $this->assertGuest();
        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);

        // ログイン後、申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 時間形式変更
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->format('Y/m/d');
        // 申請情報表示確認
        $response->assertSee('<td data-label="状態">承認待ち</td>', $escaped = false); //状態
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($correction_work['attendance_time']); //対象日時, 申請日時
        $response->assertSee('修正メッセージ'); //申請理由

        // 承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 各時間の時間形式変更
        $year = \Carbon\Carbon::parse($work['attendance_time'])->format('Y年');
        $month_day = \Carbon\Carbon::parse($work['attendance_time'])->format('n月j日');

        // 勤怠情報表示確認
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($year); //年
        $response->assertSee($month_day); //月日
        $response->assertSee('09:30'); //出勤
        $response->assertSee('18:30'); //退勤
        $response->assertSee('13:30'); //休憩開始
        $response->assertSee('14:00'); //休憩終了
        $response->assertSee('修正メッセージ'); //申請理由
    }

    public function test_申請一覧_申請後承認待ち()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 正しい値を設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        // 勤怠詳細画面へリダイレクト
        $response->assertRedirect('/attendance/1');
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 申請一覧画面へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 時間形式変更
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->format('Y/m/d');
        // 申請情報表示確認
        $response->assertSee('<td data-label="状態">承認待ち</td>', $escaped = false); //状態
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($correction_work['attendance_time']); //対象日時, 申請日時
        $response->assertSee('修正メッセージ'); //申請理由
    }

    public function test_申請一覧_管理者承認後承認済み()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 正しい値を設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        // 勤怠詳細画面へリダイレクト
        $response->assertRedirect('/attendance/1');
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 管理者ユーザーで承認画面と申請一覧画面を確認する
        // 一般ユーザーをログアウトする
        $response = $this->post('/logout');
        //ログアウトしていることを確認
        $this->assertGuest();
        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);

        // ログイン後、申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list', [
            'email' => 'abcd@example.com',
            'password' => "password",
        ]);
        $response->assertStatus(200);
        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 承認ボタン押下
        $response = $this->post('/stamp_correction_request/approve/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => '13:30',
            'rest_finish' => '14:30',
            'remarks' => '修正メッセージ',
        ]);
        $response->assertStatus(302);
        // 修正申請承認画面へリダイレクト
        $response->assertRedirect('/stamp_correction_request/approve/1');
        // 修正申請承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 申請一覧画面で承認済み表示確認
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        // 承認済みのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_approved');
        $response->assertStatus(200);

        // 時間形式変更
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($work['attendance_time'])->format('Y/m/d');
        // 申請情報表示確認
        $response->assertSee('<td data-label="状態">承認済み</td>', $escaped = false); //状態
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($correction_work['attendance_time']); //対象日時, 申請日時
        $response->assertSee('修正メッセージ'); //申請理由
    }

    public function test_申請一覧_詳細画面遷移()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/attendance';
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

        // ログイン後、勤怠一覧画面へ移動
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 正しい値を設定し、修正ボタン押下
        $response = $this->post('/attendance/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => ['13:30', null],
            'rest_finish' => ['14:00', null],
            'remarks' => '修正メッセージ',
            'rest_count' => '2'
        ]);
        $response->assertStatus(302);
        // 勤怠詳細画面へリダイレクト
        $response->assertRedirect('/attendance/1');
        // 勤怠詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 申請一覧画面へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 申請詳細画面へ移動
        $response = $this->get('/attendance/1');
        $response->assertStatus(200);

        // 各時間の時間形式変更
        $year = \Carbon\Carbon::parse($work['attendance_time'])->format('Y年');
        $month_day = \Carbon\Carbon::parse($work['attendance_time'])->format('n月j日');

        // 勤怠情報表示確認
        $response->assertSee($this->user['name']); //名前
        $response->assertSee($year); //年
        $response->assertSee($month_day); //月日
        $response->assertSee('09:30'); //出勤
        $response->assertSee('18:30'); //退勤
        $response->assertSee('13:30'); //休憩開始
        $response->assertSee('14:00'); //休憩終了
        $response->assertSee('修正メッセージ'); //申請理由
        $response->assertSee('※承認待ちのため修正はできません。'); //承認待ちメッセージ
    }
}
