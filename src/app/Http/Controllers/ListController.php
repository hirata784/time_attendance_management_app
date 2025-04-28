<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    public function index()
    {
        // データ作成
        $now = Carbon::now();
        $user_id = Auth::id();
        $now_date = $now;
        $rests = [];
        $rest_sum = [];

        // 出勤時間の年月と打刻したユーザーを検索
        $works = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 7) . '%')
            ->where('user_id', $user_id)->get();

        foreach ($works as $work) {
            // ログインユーザーの休憩情報取得
            $rest = Rest::where('work_id', $work->id)->get();
            array_push($rests, $rest);
        }

        for ($i = 0; $i < count($works); $i++) {
            // 休憩時間を追加
            // 休憩していない場合、処理しない
            if (isset($rests[$i][0]->rest_start) == false) {
                // 合計値を求めるとき使用する休憩時間を0に設定する
                $rest_seconds = 0;
            } else {
                // Restsテーブルより、休憩開始&終了時間を作成
                $rest_start = new Carbon($rests[$i][0]->rest_start);
                $rest_finish = new Carbon($rests[$i][0]->rest_finish);


                // ****************************************************
                // 複数休憩の場合、1つに時間をまとめる
                // ****************************************************

                // 差分の秒数を計算
                $rest_seconds = $rest_start->diffInSeconds($rest_finish);
                // 秒数が休憩開始 > 休憩終了の場合、1分のズレが生じるので調整する
                if ($rest_start->second > $rest_finish->second) {
                    $rest_seconds += 60;
                }
                // 秒数から時間、分を計算
                $rest_hours = floor($rest_seconds / 3600);
                $rest_minutes = floor(($rest_seconds % 3600) / 60);

                // 結果を表示
                if ($rest_minutes < 10) {
                    // 分が10未満の場合、0を１つ追加
                    $rest_time = $rest_hours . ":0" . $rest_minutes;
                } else {
                    $rest_time = $rest_hours . ":" . $rest_minutes;
                }
                // 配列要素を追加
                $works[$i]['rest_sum'] = $rest_time;
            }

            // 合計時間を追加
            // 退勤していない場合、処理しない
            if (isset($works[$i]->leaving_time) == true) {
                // 出勤&退勤時間を作成
                $attendance_time = new Carbon($works[$i]->attendance_time);
                $leaving_time = new Carbon($works[$i]->leaving_time);

                // 差分の秒数を計算
                $sum_seconds = $leaving_time->diffInSeconds($attendance_time);
                // 先程求めた休憩合計時間を引く
                $sum_seconds = $sum_seconds - $rest_seconds;
                // 秒数が出勤時間 > 退勤時間の場合、1分のズレが生じるので調整する
                if (($attendance_time->second) > ($leaving_time->second)) {
                    $sum_seconds += 60;
                }
                // 秒数から時間、分を計算
                $sum_hours = floor($sum_seconds / 3600);
                $sum_minutes = floor(($sum_seconds % 3600) / 60);

                // 結果を表示
                if ($sum_minutes < 10) {
                    // 分が10未満の場合、0を１つ追加
                    $sum_time = $sum_hours . ":0" . $sum_minutes;
                } else {
                    $sum_time = $sum_hours . ":" . $sum_minutes;
                }
                // 配列要素を追加
                $works[$i]['sum_time'] = $sum_time;
            }
        }
        return view('list', compact('works', 'now_date'));
    }

    public function indexMonth(Request $request)
    {
        // データ作成
        $works = Work::all();
        $user_id = Auth::id();
        $now_date = new Carbon($request->now_date);
        $rests = [];
        $rest_sum = [];


        if ($request->has('last-month')) {
            // 前月を表示
            $now_date->subMonth(1);
        }

        if ($request->has('next-month')) {
            // 翌月を表示
            $now_date->addMonth(1);
        }

        // 出勤時間の年月と打刻したユーザーを検索
        $works = Work::where('attendance_time', "LIKE", '%' . substr($now_date, 0, 7) . '%')
            ->where('user_id', $user_id)->get();

        foreach ($works as $work) {
            // ログインユーザーの休憩情報取得
            $rest = Rest::where('work_id', $work->id)->get();
            array_push($rests, $rest);
        }

        // 休憩時間を追加
        for ($i = 0; $i < count($works); $i++) {
            // 休憩時間を追加
            // 休憩していない場合、処理しない
            if (isset($rests[$i][0]->rest_start) == false) {
                // 合計値を求めるとき使用する休憩時間を0に設定する
                $rest_seconds = 0;
            } else {
                // Restsテーブルより、休憩開始&終了時間を作成
                $rest_start = new Carbon($rests[$i][0]->rest_start);
                $rest_finish = new Carbon($rests[$i][0]->rest_finish);

                // 差分の秒数を計算
                $rest_seconds = $rest_start->diffInSeconds($rest_finish);
                // 秒数が休憩開始 > 休憩終了の場合、1分のズレが生じるので調整する
                if ($rest_start->second > $rest_finish->second) {
                    $rest_seconds += 60;
                }
                // 秒数から時間、分を計算
                $rest_hours = floor($rest_seconds / 3600);
                $rest_minutes = floor(($rest_seconds % 3600) / 60);

                // 結果を表示
                if ($rest_minutes < 10) {
                    // 分が10未満の場合、0を１つ追加
                    $rest_time = $rest_hours . ":0" . $rest_minutes;
                } else {
                    $rest_time = $rest_hours . ":" . $rest_minutes;
                }
                // 配列要素を追加
                $works[$i]['rest_sum'] = $rest_time;
            }

            // 合計時間を追加
            // 退勤していない場合、処理しない
            if (isset($works[$i]->leaving_time) == true) {
                // 出勤&退勤時間を作成
                $attendance_time = new Carbon($works[$i]->attendance_time);
                $leaving_time = new Carbon($works[$i]->leaving_time);

                // 差分の秒数を計算
                $sum_seconds = $leaving_time->diffInSeconds($attendance_time);
                // 先程求めた休憩合計時間を引く
                $sum_seconds = $sum_seconds - $rest_seconds;
                // 秒数が出勤時間 > 退勤時間の場合、1分のズレが生じるので調整する
                if (($attendance_time->second) > ($leaving_time->second)) {
                    $sum_seconds += 60;
                }
                // 秒数から時間、分を計算
                $sum_hours = floor($sum_seconds / 3600);
                $sum_minutes = floor(($sum_seconds % 3600) / 60);

                // 結果を表示
                if ($sum_minutes < 10) {
                    // 分が10未満の場合、0を１つ追加
                    $sum_time = $sum_hours . ":0" . $sum_minutes;
                } else {
                    $sum_time = $sum_hours . ":" . $sum_minutes;
                }
                // 配列要素を追加
                $works[$i]['sum_time'] = $sum_time;
            }
        }
        return view('list', compact('works', 'now_date'));
    }
}
