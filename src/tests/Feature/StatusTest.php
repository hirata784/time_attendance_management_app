<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use DatabaseMigrations;

    // 5.ステータス確認機能
    public function test_勤怠登録_勤務外ステータス()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('勤務外');
    }

    public function test_勤怠登録_出勤中ステータス()
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

        // 画面に表示されているステータスを確認
        $response->assertSee('出勤中');
    }

    public function test_勤怠登録_休憩中ステータス()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 休憩開始時間を取得
        $rest_start = Carbon::now()->addMinute(5);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 休憩中ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => null,
        ])->create();
        $rest = Rest::factory([
            'work_id' => '1',
            'rest_start' => $rest_start,
            'rest_finish' => null,
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => null,
        ]);
        $this->assertDatabaseHas('rests', [
            'work_id' => '1',
            'rest_start' => $rest_start,
            'rest_finish' => null,
        ]);

        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('休憩中');
    }

    public function test_勤怠登録_退勤済ステータス()
    {
        // 出勤日時を取得
        $attendance_time = Carbon::now();
        // 退勤日時を取得
        $attendance_time = Carbon::now()->addMinute(5);

        // テスト用ユーザーを作成
        $user = User::factory()->create();
        // 退勤済ステータスが表示されるデータ作成
        $work = Work::factory([
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $attendance_time,
        ])->create();
        // データベースにデータが存在するかをチェック
        $this->assertDatabaseHas('works', [
            'user_id' => '1',
            'attendance_time' => $attendance_time,
            'leaving_time' => $attendance_time,
        ]);

        // ログイン後、勤怠登録画面へ移動
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // 画面に表示されているステータスを確認
        $response->assertSee('退勤済');
    }
}
