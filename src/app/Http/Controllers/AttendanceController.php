<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // データ作成
        $now = Carbon::now();
        $user_id = Auth::id();
        $work_status = '1';

        // ユーザー初回ログイン時、workテーブル作成後、時間表示だけしてリターン
        if (
            Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', $user_id)->count() == 0
        ) {
            $now_date = $now->isoFormat('Y年M月D日(ddd)');
            $now_time = $now->format('H:i');
            return view('attendance', compact('now_date', 'now_time', 'work_status'));
        } else {
            $work = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
                ->where('user_id', $user_id)
                ->first();
        }

        // 出勤時間の今日日付(年月日)と打刻したユーザーを検索(出勤中)
        $date = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', $user_id)
            ->exists();
        if ($date == true) {
            $work_status = '2';
        }

        // 休憩開始の今日日付(年月日)と打刻したユーザーを検索(休憩中)
        $date = Rest::where('rest_start', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('work_id', $work->id)
            ->exists();
        if ($date == true) {
            $work_status = '3';
        }

        // 休憩終了の今日日付(年月日)と打刻したユーザーを検索(出勤中)
        $date = Rest::orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->where('work_id', $work->id)
            ->first();

        // 未休憩の場合、 $date->rest_finishでエラーが起きるので処理しない
        if ($date != null) {
            // 休憩終了に値が入っている場合、出勤中を表示
            if ($date->rest_finish != null) {
                $work_status = '2';
            }
        }

        // 退勤時間の今日日付(年月日)と打刻したユーザーを検索(退勤済)
        $date = Work::where('leaving_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', $user_id)->exists();
        if ($date == true) {
            $work_status = '4';
        }

        // 今日日付の画面表示
        $now_date = $now->isoFormat('Y年M月D日(ddd)');
        $now_time = $now->format('H:i');
        return view('attendance', compact('now_date', 'now_time', 'work_status', 'work'));
    }

    public function store()
    {
        // データ作成
        $user_id = Auth::id();
        $now = Carbon::now();
        $attendance_time = substr($now, 0, 16) . ":00";

        // 打刻処理
        $form = [
            'user_id' => $user_id,
            'attendance_time' => $attendance_time,
        ];
        Work::create($form);
        return redirect()->back();
    }

    public function updateWork(Request $request)
    {
        // データ作成
        $user_id = Auth::id();
        // 日付：Carbon 時間：画面表示を組み合わせて現在日時を作成
        $now_date = substr(Carbon::now(), 0, 10);
        $now_time = $request['now_time'];
        $now = $now_date . ' ' . $now_time;

        // 退勤処理
        if ($request->has('work')) {
            $leaving_time = substr($now, 0, 16) . ":00";
            // 出勤時間の今日日付(年月日)と打刻したユーザーを検索
            $date = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
                ->where('user_id', $user_id)
                ->first();

            // 打刻
            $form = [
                'leaving_time' => $leaving_time,
            ];
            Work::find($date->id)->update($form);
        }

        // 休憩処理
        if ($request->has('rest')) {
            $rest_start = substr($now, 0, 16) . ":00";
            // work_id:ログインユーザーidかつ今日日付の出勤時間
            $work_id = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
                ->where('user_id', Auth::id())
                ->first();

            // 打刻
            $form = [
                'rest_start' => $rest_start,
                'work_id' => $work_id->id,
            ];
            Rest::create($form);
        }
        return redirect()->back();
    }

    public function updateRest(Request $request)
    {
        // データ作成
        $now_date = substr(Carbon::now(), 0, 10);
        // 日付：Carbon 時間：画面表示を組み合わせて現在日時を作成
        $now_date = substr(Carbon::now(), 0, 10);
        $now_time = $request['now_time'];
        $now = $now_date . ' ' . $now_time;
        $rest_finish = substr($now, 0, 16) . ":00";

        // work_id:ログインユーザーidかつ今日日付の出勤時間
        $work_id = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', Auth::id())
            ->first();
        // 該当work_idの最新休憩開始時間が入った行を検索
        $row = Rest::orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->where('work_id', $work_id->id)
            ->first();

        // 打刻
        $form = [
            'rest_finish' => $rest_finish,
        ];
        Rest::find($row->id)->update($form);
        return redirect()->back();
    }
}
