<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Work;
use App\Models\User;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create()
    {
        // データ作成
        $now = Carbon::now();
        $user = User::where('id', Auth::id());
        $work_status = '1';

        // ユーザー初回ログイン時、時間表示だけしてリターン
        if (
            Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', Auth::id())->count() == 0
        ) {
            $now_date = $now->isoFormat('Y年M月D日(ddd)');
            $now_time = $now->format('H:i');
            return view('attendance', compact('now_date', 'now_time', 'work_status'));
        } else {
            $work_id = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
                ->where('user_id', Auth::id())
                ->first()
                ->id;
        }

        // 出勤時間の今日日付(年月日)と打刻したユーザーを検索(出勤中)
        $date = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', Auth::id())->exists();
        if ($date == true) {
            $work_status = '2';
            $date = false;
        }

        // 休憩開始の今日日付(年月日)と打刻したユーザーを検索(休憩中)
        $date = Rest::where('rest_start', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('work_id', $work_id)->exists();
        if ($date == true) {
            $work_status = '3';
            $date = false;
        }

        // 休憩終了の今日日付(年月日)と打刻したユーザーを検索(出勤中)
        $date = Rest::orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->where('work_id', $work_id)
            ->first();

        // 未休憩の場合、 $date->rest_finishでエラーが起きるので処理しない
        if ($date != null) {
            // 休憩終了に値が入っている場合、出勤中を表示
            if ($date->rest_finish != null) {
                $work_status = '2';
                $date = false;
            }
        }

        // 退勤時間の今日日付(年月日)と打刻したユーザーを検索(退勤済)
        $date = Work::where('leaving_time', "LIKE", '%' . substr($now, 0, 10) . '%')
            ->where('user_id', Auth::id())->exists();
        if ($date == true) {
            $work_status = '4';
        }

        // 今日日付の画面表示
        $now_date = $now->isoFormat('Y年M月D日(ddd)');
        $now_time = $now->format('H:i');
        return view('attendance', compact('now_date', 'now_time', 'work_status'));
    }

    public function store()
    {
        // データ作成
        $user_id = Auth::id();
        $now = Carbon::now();

        // 打刻処理
        $form = [
            'user_id' => $user_id,
            'attendance_time' => $now,
        ];
        Work::create($form);
        return redirect()->back();
    }

    public function store2(Request $request)
    {
        // 退勤処理
        if ($request->has('work')) {
            // データ作成
            $user_id = Auth::id();
            $now = Carbon::now();

            // 出勤時間の今日日付(年月日)と打刻したユーザーを検索
            $date = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')->where('user_id', Auth::id())->first();

            // 打刻
            $form = [
                'leaving_time' => $now,
            ];
            Work::find($date->id)->update($form);
        }

        // 休憩処理
        if ($request->has('rest')) {
            // データ作成
            $now = Carbon::now();

            // work_id:ログインユーザーidかつ今日日付の出勤時間
            $work_id = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')
                ->where('user_id', Auth::id())
                ->first();

            // 出勤時間の今日日付(年月日)と打刻したユーザーを検索
            $date = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 10) . '%')->where('user_id', Auth::id())->first();

            // 打刻
            $form = [
                'rest_start' => $now,
                'work_id' => $work_id->id,
            ];
            Rest::create($form);
        }
        return redirect()->back();
    }

    public function store3(Request $request)
    {
        // 休憩処理
        // データ作成
        $now = Carbon::now();
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
            'rest_finish' => $now,
        ];
        Rest::find($row->id)->update($form);
        return redirect()->back();
    }
}
