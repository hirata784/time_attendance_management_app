<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use App\Models\Correction_work;
use App\Models\Correction_rest;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 15.勤怠情報修正機能(管理者)
    public function test_申請一覧_承認待ち()
    {
        // データを取得
        $now_date = Carbon::now();
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成(2つ)
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

        // 修正待ちデータを作成
        $correction_work = Correction_work::factory([
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $correction_rest = Correction_rest::factory([
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
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

        // 修正待ちデータ
        $this->assertDatabaseHas('correction_works', [
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('correction_rests', [
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
        ]);

        // 管理者
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);

        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);
        // 申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);

        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 時間形式変更
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($correction_work['attendance_time'])->format('Y/m/d');
        $correction_work['application_date'] = \Carbon\Carbon::parse($correction_work['application_date'])->format('Y/m/d');

        // 申請情報表示確認
        $response->assertSee('<td data-label="状態">承認待ち</td>', $escaped = false); //状態
        $response->assertSee($user['name']); //名前
        $response->assertSee($correction_work['attendance_time']); //対象日時
        $response->assertSee($correction_work['remarks']); //申請理由
        $response->assertSee($correction_work['application_date']); //申請日時
    }

    public function test_申請一覧_承認済み()
    {
        // データを取得
        $now_date = Carbon::now();
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成(2つ)
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

        // 修正済みデータを作成
        $correction_work = Correction_work::factory([
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $correction_rest = Correction_rest::factory([
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
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

        // 修正済みデータ
        $this->assertDatabaseHas('correction_works', [
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '2',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('correction_rests', [
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
        ]);

        // 管理者
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);

        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);
        // 申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);

        // 承認済みのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_approved');
        $response->assertStatus(200);

        // 時間形式変更
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($correction_work['attendance_time'])->format('Y/m/d');
        $correction_work['application_date'] = \Carbon\Carbon::parse($correction_work['application_date'])->format('Y/m/d');

        // 申請情報表示確認
        $response->assertSee('<td data-label="状態">承認済み</td>', $escaped = false); //状態
        $response->assertSee($user['name']); //名前
        $response->assertSee($correction_work['attendance_time']); //対象日時
        $response->assertSee($correction_work['remarks']); //申請理由
        $response->assertSee($correction_work['application_date']); //申請日時
    }

    public function test_申請一覧_修正申請詳細画面遷移()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/stamp_correction_request';
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成(2つ)
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

        // 修正待ちデータを作成
        $correction_work = Correction_work::factory([
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $correction_rest = Correction_rest::factory([
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
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
        // 修正待ちデータ
        $this->assertDatabaseHas('correction_works', [
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('correction_rests', [
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
        ]);
        // 管理者
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);

        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);
        // 申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);

        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 修正申請承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 各時間の時間形式変更
        $year = \Carbon\Carbon::parse($correction_work['attendance_time'])->format('Y年');
        $month_day = \Carbon\Carbon::parse($correction_work['attendance_time'])->format('n月j日');
        $correction_work['attendance_time'] = \Carbon\Carbon::parse($correction_work['attendance_time'])->Format('H:i');
        $correction_work['leaving_time'] = \Carbon\Carbon::parse($correction_work['leaving_time'])->Format('H:i');
        $correction_rest['rest_start'] = \Carbon\Carbon::parse($correction_rest['rest_start'])->Format('H:i');
        $correction_rest['rest_finish'] = \Carbon\Carbon::parse($correction_rest['rest_finish'])->Format('H:i');

        // 勤怠情報表示確認
        $response->assertSee($user['name']); //名前
        $response->assertSee($year); //年
        $response->assertSee($month_day); //月日
        $response->assertSee($correction_work['attendance_time']); //出勤
        $response->assertSee($correction_work['leaving_time']); //退勤
        $response->assertSee($correction_rest['rest_start']); //休憩開始
        $response->assertSee($correction_rest['rest_finish']); //休憩終了
        $response->assertSee($correction_work['remarks']); //申請理由
    }

    public function test_申請一覧_承認処理()
    {
        // データを取得
        $now_date = Carbon::now();
        // リファラーを手動で設定
        $_SERVER['HTTP_REFERER'] = 'http://localhost/stamp_correction_request';
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成(2つ)
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

        // 修正待ちデータを作成
        $correction_work = Correction_work::factory([
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ])->create();
        $correction_rest = Correction_rest::factory([
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
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
        // 修正待ちデータ
        $this->assertDatabaseHas('correction_works', [
            'user_id' => '1',
            'work_id' => '1',
            'application_status' => '1',
            'attendance_time' => substr($now_date, 0, 10) . ' 09:30:00',
            'leaving_time' => substr($now_date, 0, 10) . ' 18:30:00',
            'remarks' => '承認待ちメッセージ',
            'application_date' => substr($now_date, 0, 10) . ' 19:00:00',
        ]);
        $this->assertDatabaseHas('correction_rests', [
            'correction_work_id' => '1',
            'rest_start' => substr($now_date, 0, 10) . ' 12:30:00',
            'rest_finish' => substr($now_date, 0, 10) . ' 13:30:00',
        ]);
        // 管理者
        $this->assertDatabaseHas('admins', [
            'name' => 'testuser',
            'email' => 'abcd@example.com',
            'password' => 'password',
        ]);

        // 管理者ログイン(if (Auth::guard('admin')->check()通過用)
        Auth::guard('admin')->login($admin);

        // 承認待ち：勤怠一覧画面には修正前の勤怠情報が記載される
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
            $before_rest_time = $rest_hours . ":0" . $rest_minutes;
        } else {
            $before_rest_time = $rest_hours . ":" . $rest_minutes;
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
            $before_sum_time = $sum_hours . ":0" . $sum_minutes;
        } else {
            $before_sum_time = $sum_hours . ":" . $sum_minutes;
        }

        // 出勤退勤時間の時間形式変更
        $before_attendance_time = \Carbon\Carbon::parse($work['attendance_time'])->Format('H:i');
        $before_leaving_time = \Carbon\Carbon::parse($work['leaving_time'])->Format('H:i');
        // 勤怠情報表示確認
        $response->assertSee($before_attendance_time); //出勤
        $response->assertSee($before_leaving_time); //退勤
        $response->assertSee($before_rest_time); //休憩
        $response->assertSee($before_sum_time); //合計

        // 申請一覧画面(管理者)へ移動
        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);

        // 承認待ちのタブを押下
        $response = $this->get('/stamp_correction_request/list/index_wait');
        $response->assertStatus(200);

        // 修正申請承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 承認ボタン押下
        $response = $this->post('/stamp_correction_request/approve/1/update', [
            'attendance_time' => '09:30',
            'leaving_time' => '18:30',
            'rest_start' => '12:30',
            'rest_finish' => '13:30',
            'remarks' => '承認待ちメッセージ',
        ]);
        $response->assertStatus(302);
        // 修正申請承認画面へリダイレクト
        $response->assertRedirect('/stamp_correction_request/approve/1');
        // 修正申請承認画面へ移動
        $response = $this->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);

        // 承認済み：勤怠一覧画面には修正後の勤怠情報が記載される
        // スタッフ別勤怠一覧画面へ移動
        $response = $this->get('/admin/attendance/staff/1');
        $response->assertStatus(200);

        // 休憩合計時間
        // 休憩開始&終了時間を作成
        $correction_rest['rest_start'] = new Carbon($correction_rest['rest_start']);
        $correction_rest['rest_finish'] = new Carbon($correction_rest['rest_finish']);

        // 差分の分数を計算
        $rest_minute = $correction_rest['rest_finish']->diffInMinutes($correction_rest['rest_start']);
        // 分数から時間、分を計算
        $rest_hours = floor($rest_minute / 60);
        $rest_minutes = floor($rest_minute % 60);
        // 結果を表示
        if ($rest_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $after_rest_time = $rest_hours . ":0" . $rest_minutes;
        } else {
            $after_rest_time = $rest_hours . ":" . $rest_minutes;
        }

        // 勤務合計時間
        // 差分の分数を計算
        $correction_work['attendance_time'] = new Carbon($correction_work['attendance_time']);
        $correction_work['leaving_time'] = new Carbon($correction_work['leaving_time']);
        $sum_minute = $correction_work['leaving_time']->diffInMinutes($correction_work['attendance_time']);
        // 先程求めた休憩合計時間を引く
        $sum_minute = $sum_minute - $rest_minute;
        // 分数から時間、分を計算
        $sum_hours = floor($sum_minute / 60);
        $sum_minutes = floor($sum_minute % 60);
        // 結果を表示
        if ($sum_minutes < 10) {
            // 分が10未満の場合、0を１つ追加
            $after_sum_time = $sum_hours . ":0" . $sum_minutes;
        } else {
            $after_sum_time = $sum_hours . ":" . $sum_minutes;
        }

        // 出勤退勤時間の時間形式変更
        $after_attendance_time = \Carbon\Carbon::parse($correction_work['attendance_time'])->Format('H:i');
        $after_leaving_time = \Carbon\Carbon::parse($correction_work['leaving_time'])->Format('H:i');
        // 勤怠情報表示確認
        $response->assertSee($after_attendance_time); //出勤
        $response->assertSee($after_leaving_time); //退勤
        $response->assertSee($after_rest_time); //休憩
        $response->assertSee($after_sum_time); //合計
    }
}
