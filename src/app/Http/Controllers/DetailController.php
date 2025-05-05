<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Work;
use App\Models\User;
use App\Models\Rest;
use App\Models\Correction_work;
use App\Models\Correction_rest;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\AttendanceRequest;

use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index($work_id)
    {
        // データ作成
        $user_id = Auth::id();
        $list = [];
        // 承認ステータスを確認
        $correction_work = Correction_work::where('work_id', $work_id)->get();
        if (isset($correction_work[0]['application_status']) == true) {
            // 承認待ち・承認済みの場合
            // 休憩回数
            $rest_count = Correction_rest::where('work_id', $work_id)->get();
            // 名前
            $name = User::where('id', $user_id)->get();
            $list['name'] = $name[0]->name;
            // 日付
            $attendance_time = $correction_work[0]['attendance_time'];
            $list['year'] = \Carbon\Carbon::parse($attendance_time)->format('Y年');
            $list['month_day'] = \Carbon\Carbon::parse($attendance_time)->format('n月j日');
            // 勤務時間
            $list['attendance_time'] = \Carbon\Carbon::parse($attendance_time)->format('H:i');
            $leaving_time = $correction_work[0]['leaving_time'];
            // 退勤していない場合、ボックス内空白
            if ($leaving_time == null) {
                $list['leaving_time'] = "";
            } else {
                $list['leaving_time'] = \Carbon\Carbon::parse($leaving_time)->format('H:i');
            }
            // 備考
            $list['remarks'] = $correction_work[0]['remarks'];

            // 休憩時間
            for ($i = 0; $i < count($rest_count); $i++) {
                $list['rest_start'][$i] = \Carbon\Carbon::parse($rest_count[$i]->rest_start)->format('H:i');
                // 休憩終了していない場合、ボックス内空白
                if ($rest_count[$i]->rest_finish == null) {
                    $list['rest_finish'][$i] = "";
                } else {
                    $list['rest_finish'][$i] = \Carbon\Carbon::parse($rest_count[$i]->rest_finish)->format('H:i');
                }
            }
        } else {
            // 承認待ちじゃない場合
            // 休憩回数
            $rest_count = Rest::where('work_id', $work_id)->get();
            // 名前
            $name = User::where('id', $user_id)->get();
            $list['name'] = $name[0]->name;
            // 日付
            $attendance_time = Work::all()->find($work_id)->attendance_time;
            $list['year'] = \Carbon\Carbon::parse($attendance_time)->format('Y年');
            $list['month_day'] = \Carbon\Carbon::parse($attendance_time)->format('n月j日');
            // 勤務時間
            $list['attendance_time'] = \Carbon\Carbon::parse($attendance_time)->format('H:i');
            $leaving_time = Work::all()->find($work_id)->leaving_time;
            // 退勤していない場合、ボックス内空白
            if ($leaving_time == null) {
                $list['leaving_time'] = "";
            } else {
                $list['leaving_time'] = \Carbon\Carbon::parse($leaving_time)->format('H:i');
            }
            // 備考
            $list['remarks'] = null;

            // 休憩時間
            for ($i = 0; $i < count($rest_count); $i++) {
                $list['rest_start'][$i] = \Carbon\Carbon::parse($rest_count[$i]->rest_start)->format('H:i');
                // 休憩終了していない場合、ボックス内空白
                if ($rest_count[$i]->rest_finish == null) {
                    $list['rest_finish'][$i] = "";
                } else {
                    $list['rest_finish'][$i] = \Carbon\Carbon::parse($rest_count[$i]->rest_finish)->format('H:i');
                }
            }
        }
        return view('detail', compact('work_id', 'list', 'rest_count'));
    }

    public function store($work_id, AttendanceRequest $request)
    {
        // 勤務時間の修正データ作成
        // ユーザーid
        $user_id = Auth::id();
        // 申請日
        $application_date = Carbon::now()->format('Y-m-d H:i:s');
        // 申請ステータス(1：承認待ち 2：承認済み)
        $application_status = 1;

        // 出勤・退勤時間
        $correction_date = $request->only(['attendance_time', 'leaving_time']);
        // worksテーブルから出勤時間を呼び出す
        $work_attendance_time = Work::all()->find($work_id)->attendance_time;
        // 呼び出した年月日に、修正後の時間を後ろにくっつける(秒は0で統一)
        $attendance_time = substr($work_attendance_time, 0, 11) . $correction_date['attendance_time'] . ":00";
        // worksテーブルから退勤時間を呼び出す
        $work_leaving_time = Work::all()->find($work_id)->leaving_time;
        // 呼び出した年月日に、修正後の時間を後ろにくっつける(秒は0で統一)
        $leaving_time = substr($work_leaving_time, 0, 11) . $correction_date['leaving_time'] . ":00";

        // 備考
        $correction_work = $request->only(['remarks']);
        // 用意したデータを$correction_workへまとめる
        $correction_work['user_id'] = $user_id;
        $correction_work['work_id'] = $work_id;
        $correction_work['application_date'] = $application_date;
        $correction_work['application_status'] = $application_status;
        $correction_work['attendance_time'] = $attendance_time;
        $correction_work['leaving_time'] = $leaving_time;
        // correction_worksに用意したデータでレコード追加
        Correction_work::create($correction_work);

        // 休憩時間の修正データ作成
        // 休憩開始・終了時間
        $rests = $request->only(['rest_start', 'rest_finish']);
        // 休憩回数
        $rest_count = $request->rest_count;

        for ($i = 0; $i < $rest_count; $i++) {
            // 追加分の休憩開始が空白の場合、処理しない
            if ($rests['rest_start'][$i] == null) {
                continue;
            } else {
                // 勤務時間データで呼び出した年月日に、修正後の時間を後ろにくっつける(秒は0で統一)
                $rest_start = substr($work_attendance_time, 0, 11) . $rests['rest_start'][$i] . ":00";
                $rest_finish = substr($work_attendance_time, 0, 11) . $rests['rest_finish'][$i] . ":00";

                // 用意したデータを$correction_restへまとめる
                $correction_rests[$i]['work_id'] = $work_id;
                $correction_rests[$i]['rest_start'] = $rest_start;
                $correction_rests[$i]['rest_finish'] = $rest_finish;
            }
        }
        // correction_restsに用意したデータでレコード追加
        foreach ($correction_rests as $correction_rest) {
            Correction_rest::create($correction_rest);
        }
        return redirect()->back();
    }
}
