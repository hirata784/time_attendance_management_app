<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use Carbon\Carbon;
use Tests\TestCase;

class RestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 7.休憩機能
    public function test_勤怠登録_休憩ボタン機能()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
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

        // 休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
    }

    public function test_勤怠登録_休憩複数可能()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
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

        // 休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 休憩戻の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
        // 休憩戻ボタンの表示を確認
        $response->assertSee('休憩戻');
        // 休憩戻ボタンを押下
        $response = $this->post('/attendance/update_rest');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 出勤後、勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているボタンを確認
        $response->assertSee('休憩入');
    }

    public function test_勤怠登録_休憩戻ボタン機能()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
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

        // 休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 休憩戻の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
        // 休憩戻ボタンの表示を確認
        $response->assertSee('休憩戻');
        // 休憩戻ボタンを押下
        $response = $this->post('/attendance/update_rest');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 出勤後、勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
    }

    public function test_勤怠登録_休憩戻複数可能()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
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

        // 休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 休憩戻の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
        // 休憩戻ボタンの表示を確認
        $response->assertSee('休憩戻');
        // 休憩戻ボタンを押下
        $response = $this->post('/attendance/update_rest');
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 出勤後、勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 再度休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '']);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 休憩戻ボタンの表示を確認
        $response->assertSee('休憩戻');
    }

    public function test_勤怠登録_休憩時刻確認()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 休憩開始時間を取得(出勤開始の10分後に設定)
        $rest_start = Carbon::now()->addMinute(10)->format('H:i');
        // 休憩終了時間を取得(出勤開始の20分後に設定)
        $rest_finish = Carbon::now()->addMinute(20)->format('H:i');
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

        // 休憩入の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
        // 休憩入ボタンの表示を確認
        $response->assertSee('休憩入');
        // 休憩入ボタンを押下
        $response = $this->post('/attendance/update_work', ['rest' => '', 'now_time' => $rest_start]);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');
        // 勤怠登録画面を表示
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 休憩戻の処理を行う
        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
        // 休憩戻ボタンの表示を確認
        $response->assertSee('休憩戻');
        // 休憩戻ボタンを押下
        $response = $this->post('/attendance/update_rest', ['now_time' => $rest_finish]);
        $response->assertStatus(302);
        // 勤怠登録画面へリダイレクト
        $response->assertRedirect('/attendance');

        // 勤怠一覧画面を表示
        $response = $this->get('/attendance/list');
        // 休憩合計時間を取得
        $rest_start = new Carbon($rest_start);
        $rest_finish = new Carbon($rest_finish);
        $rest_sum = $rest_finish->diffInMinutes($rest_start);
        // 休憩の合計時間表示を確認
        $response->assertSee($rest_sum);
        $response->dd();
    }
}
