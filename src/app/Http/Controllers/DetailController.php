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
    public function index($id, Request $request)
    {
        // どのページから遷移してきたかチェック
        $url = $_SERVER['HTTP_REFERER'];

        if (strpos($url, 'http://localhost/attendance') !== false or strpos($url, 'http://localhost/admin/attendance') !== false) {
            // 勤怠一覧画面から
            // データ作成(Worksのidを元に)
            $user_id = Work::find($id)->user_id;
            $work_id  = $id;
            $list = [];
            // Correction_workのデータを確認。昇順に並び替える
            $correction_work = Correction_work::orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->where('work_id', $work_id)
                ->get();
        } elseif (strpos($url, 'http://localhost/stamp_correction_request') !== false) {
            // 申請一覧画面から
            // データ作成(Correction_worksのidを元に)
            $user_id = Correction_work::find($id)->user_id;
            $work_id = Correction_work::find($id)->work_id;
            $list = [];
            // Correction_workのデータを確認
            $correction_work = Correction_work::where('id', $id)
                ->get();
        }

        if (isset($correction_work[0]['application_status']) == true) {
            if ($correction_work[0]['application_status'] == 1) {
                // 承認待ちの場合、申請中のデータを表示
                // 休憩回数
                $rest_count = Correction_work::find($correction_work[0]['id'])->correction_rests;
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
                // 承認ステータス
                $list['application_status'] = 1;
                // 修正可否(0:NG 1:OK)
                if (strpos($url, 'http://localhost/admin/attendance') !== false) {
                    // 勤怠一覧画面(管理者)から 修正OK
                    $list['change'] = 1;
                } else {
                    $list['change'] = 0;
                }
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
            } elseif ($correction_work[0]['application_status'] == 2) {
                // 承認済みの場合、勤務時間と休憩テーブルデータを表示
                // 休憩回数
                $rest_count = Correction_work::find($correction_work[0]['id'])->correction_rests;
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
                // 備考 遷移元によって表示を変更
                if (strpos($url, 'http://localhost/attendance') !== false or strpos($url, 'http://localhost/admin/attendance') !== false) {
                    // 勤怠一覧画面から 空白
                    $list['remarks'] = null;
                } elseif (strpos($url, 'http://localhost/stamp_correction_request/list') !== false) {
                    // 申請一覧画面から correction_workのremarks
                    $list['remarks'] = $correction_work[0]['remarks'];
                }
                // 承認ステータス
                $list['application_status'] = 2;
                // 修正可否(0:NG 1:OK)
                if (strpos($url, 'http://localhost/stamp_correction_request/list') !== false) {
                    // 申請一覧画面から 修正NG
                    $list['change'] = 0;
                } elseif (strpos($url, 'http://localhost/attendance') !== false or strpos($url, 'http://localhost/admin/attendance') !== false) {
                    // 勤怠一覧画面から 修正OK
                    $list['change'] = 1;
                }
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
        } else {
            // 未承認の場合、勤務時間と休憩テーブルデータを表示
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
            // 承認ステータス
            $list['application_status'] = null;
            // 修正可否(0:NG 1:OK)
            $list['change'] = 1;
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

    public function update($work_id, AttendanceRequest $request)
    {
        // 勤務時間の修正データ作成
        // ユーザーid
        $user_id = Work::find($work_id)->user_id;
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

        if (Auth::guard('admin')->check()) {
            // 管理者ログインの場合、直接修正する
            Work::find($work_id)->update($correction_work);
        } else {
            // 一般ログインの場合、修正申請する
            Correction_work::create($correction_work);
        }

        // 休憩時間の修正データ作成
        // 休憩開始・終了時間
        $rests = $request->only(['rest_start', 'rest_finish']);
        // 休憩回数
        $rest_count = $request['rest_count'];

        for ($i = 0; $i < $rest_count; $i++) {
            // 追加分の休憩開始が空白の場合、処理しない
            if ($rests['rest_start'][$i] == null) {
                continue;
            } else {
                // 勤務時間データで呼び出した年月日に、修正後の時間を後ろにくっつける(秒は0で統一)
                $rest_start = substr($work_attendance_time, 0, 11) . $rests['rest_start'][$i] . ":00";
                $rest_finish = substr($work_attendance_time, 0, 11) . $rests['rest_finish'][$i] . ":00";

                // 用意したデータを$correction_restへまとめる
                // 管理者ログインの場合、work_id
                if (Auth::guard('admin')->check()) {
                    $correction_rests[$i]['work_id'] = $work_id;
                } else {
                    // 一般ログインの場合、correction_work_id
                    $correction_rests[$i]['correction_work_id'] = Correction_work::count();
                }
                $correction_rests[$i]['rest_start'] = $rest_start;
                $correction_rests[$i]['rest_finish'] = $rest_finish;
            }
        }

        // correction_restsに用意したデータでレコード追加
        // 管理者ログインの場合、該当work_idの休憩データを削除
        if (Auth::guard('admin')->check()) {
            Rest::where('work_id', $work_id)->delete();
        }

        foreach ($correction_rests as $correction_rest) {
            if (Auth::guard('admin')->check()) {
                // 管理者ログインの場合、直接修正
                Rest::create($correction_rest);
            } else {
                // 一般ログインの場合、修正申請する
                Correction_rest::create($correction_rest);
            }
        }
        return redirect()->back();
    }
}