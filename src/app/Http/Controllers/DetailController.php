<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\AttendanceRequest;


use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index($work_id)
    {
        // データ作成
        $id = $work_id;
        $user_id = Auth::id();
        $list = [];
        $rest_count = Rest::where('work_id', $id)->get();

        // 名前
        $name = User::where('id', $user_id)->get();
        $list['name'] = $name[0]->name;
        // 日付
        $attendance_time = Work::all()->find($id)->attendance_time;
        $list['year'] = \Carbon\Carbon::parse($attendance_time)->format('Y年');
        $list['month_day'] = \Carbon\Carbon::parse($attendance_time)->format('n月j日');
        // 勤務時間
        $list['attendance_time'] = \Carbon\Carbon::parse($attendance_time)->format('H:i');
        $leaving_time = Work::all()->find($id)->leaving_time;
        // 退勤していない場合、ボックス内空白
        if ($leaving_time == null) {
            $list['leaving_time'] = "";
        } else {
            $list['leaving_time'] = \Carbon\Carbon::parse($leaving_time)->format('H:i');
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
        return view('detail', compact('work_id', 'list', 'rest_count'));
    }
}
