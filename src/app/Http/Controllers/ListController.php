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
        $rests = Rest::all();
        $user_id = Auth::id();
        $now_date = $now;

        // 出勤時間の年月と打刻したユーザーを検索
        $works = Work::where('attendance_time', "LIKE", '%' . substr($now, 0, 7) . '%')
            ->where('user_id', $user_id)->get();

        return view('list', compact('works', 'rests', 'now_date'));
    }

    public function indexMonth(Request $request)
    {
        // データ作成
        $works = Work::all();
        $rests = Rest::all();
        $user_id = Auth::id();
        $now_date = new Carbon($request->now_date);

        if ($request->has('last-month')) {
            // 前月を表示
            $now_date->subMonth(1);
        }

        if ($request->has('next-month')) {
            // 翌月を表示
            $now_date->addMonth(1);
        }

        $works = Work::where('attendance_time', "LIKE", '%' . substr($now_date, 0, 7) . '%')
            ->where('user_id', $user_id)->get();
        return view('list', compact('works', 'rests', 'now_date'));
    }
}
