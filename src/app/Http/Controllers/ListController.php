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
        $user_id = Auth::id();
        $now_date = Carbon::now();
        $rests = [];
        $rest_sum = [];
        $rest_minute = 0;
        $lists = [];

        // 出勤時間の年月と打刻したユーザーを検索
        $works = Work::where('attendance_time', "LIKE", '%' . substr($now_date, 0, 7) . '%')
            ->where('user_id', $user_id)->get();

        foreach ($works as $work) {
            // ログインユーザーの休憩情報取得
            $rest = Rest::where('work_id', $work->id)->get();
            array_push($rests, $rest);
        }

        if (count($works) == 0) {
            // 該当データがない場合
            $lists = [];
        } else {
            $lists = $this->list($works, $rests, $rest_minute);
        }
        return view('list', compact('lists', 'now_date'));
    }

    public function indexMonth(Request $request)
    {
        // データ作成
        $works = Work::all();
        $user_id = Auth::id();
        $now_date = new Carbon($request['now_date']);
        $rests = [];
        $rest_sum = [];
        $rest_minute = 0;
        $lists = [];

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

        if (count($works) == 0) {
            // 該当データがない場合
            $lists = [];
        } else {
            $lists = $this->list($works, $rests, $rest_minute);
        }
        return view('list', compact('lists', 'now_date'));
    }


    public function list($works, $rests, $rest_minute)
    {
        for ($i = 0; $i < count($works); $i++) {
            // work_id
            $work_id = $works[$i]['id'];
            // 出勤時間
            $attendance_time = new Carbon($works[$i]->attendance_time);
            // 配列要素を追加
            $lists[$i]['work_id'] = $work_id;
            $lists[$i]['date'] = \Carbon\Carbon::parse($attendance_time)->isoFormat('MM/DD(ddd)');
            $lists[$i]['attendance_time'] = \Carbon\Carbon::parse($attendance_time)->format('H:i');
            // 休憩合計時間にnullを代入(未休憩対策)
            $lists[$i]['rest_sum'] = null;
            for ($j = 0; $j < count($rests[$i]); $j++) {
                // 休憩時間を追加(複数休憩の場合、1つに時間をまとめる)
                // 休憩していない場合、処理しない
                if (isset($rests[$i][0]->rest_finish) == false) {
                    // 合計値を求めるとき使用する休憩時間を0に設定する
                    $rest_minute = 0;
                } else {
                    // Restsテーブルより、休憩開始&終了時間を作成
                    $rest_start = new Carbon($rests[$i][$j]->rest_start);
                    $rest_finish = new Carbon($rests[$i][$j]->rest_finish);

                    // 差分の分数を計算
                    $rest_minute += $rest_finish->diffInMinutes($rest_start);
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
                    // 配列要素を追加
                    $lists[$i]['rest_sum'] = $rest_time;
                }
            }

            // 合計時間を追加
            if (isset($works[$i]->leaving_time) == true) {
                // 退勤時間を作成
                $leaving_time = new Carbon($works[$i]->leaving_time);
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
                // 配列要素を追加
                $lists[$i]['leaving_time'] = \Carbon\Carbon::parse($leaving_time)->format('H:i');
                $lists[$i]['sum_time'] = $sum_time;
            } else {
                // 退勤していない場合、空白を追加
                $lists[$i]['leaving_time'] = null;
                $lists[$i]['sum_time'] = null;
            }
            $rest_minute = 0;
        }
        return $lists;
    }
}
