<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Correction_work;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        // データ作成
        $data = null;
        if (Auth::guard('admin')->check()) {
            // 管理者のログイン場合：全ての一般ユーザーを表示
            $user = User::all();
            // 全一般ユーザーを抽出
            $correction_work = Correction_work::all();
        } else {
            // 一般ログインの場合：ログイン中のユーザーのみ表示
            $user_id = Auth::id();
            $user = User::where('id', $user_id)->get();
            // ログインユーザーを抽出
            $correction_work = Correction_work::where('user_id', $user_id)
                ->get();
        }
        if (count($correction_work) == 0) {
            // 該当データがない場合
            $lists = [];
        } else {
            $lists = $this->approvalList($user, $correction_work);
        }
        return view('request', compact('lists', 'data'));
    }

    public function indexWait()
    {
        // データ作成
        $data = "wait";
        if (Auth::guard('admin')->check()) {
            // 管理者のログイン場合：全ての一般ユーザーを表示
            $user = User::all();
            // 全一般ユーザーかつ承認待ちを抽出
            $correction_work = Correction_work::where('application_status', 1)
                ->get();
        } else {
            // 一般ログインの場合：ログイン中のユーザーのみ表示
            $user_id = Auth::id();
            $user = User::where('id', $user_id)->get();
            // ログインユーザーかつ承認待ちを抽出
            $correction_work = Correction_work::where('user_id', $user_id)
                ->where('application_status', 1)
                ->get();
        }
        if (count($correction_work) == 0) {
            // 該当データがない場合
            $lists = [];
        } else {
            $lists = $this->approvalList($user, $correction_work);
        }
        return view('request', compact('lists', 'data'));
    }

    public function indexApproved()
    {
        // データ作成
        $data = "approved";
        if (Auth::guard('admin')->check()) {
            // 管理者のログイン場合：全ての一般ユーザーを表示
            $user = User::all();
            // 全一般ユーザーかつ承認済みを抽出
            $correction_work = Correction_work::where('application_status', 2)
                ->get();
        } else {
            // 一般ログインの場合：ログイン中のユーザーのみ表示
            $user_id = Auth::id();
            $user = User::where('id', $user_id)->get();
            // ログインユーザーかつ承認済みを抽出
            $correction_work = Correction_work::where('user_id', $user_id)
                ->where('application_status', 2)
                ->get();
        }
        if (count($correction_work) == 0) {
            // 該当データがない場合
            $lists = [];
        } else {
            $lists = $this->approvalList($user, $correction_work);
        }
        return view('request', compact('lists', 'data'));
    }

    public function approvalList($user, $correction_work)
    {
        for ($i = 0; $i < count($correction_work); $i++) {
            // id
            $lists[$i]['id'] = $correction_work[$i]['id'];
            // work_id
            $lists[$i]['work_id'] = $correction_work[$i]['work_id'];
            // 状態
            if ($correction_work[$i]['application_status'] == 1) {
                // application_statusが1の時、[承認待ち]を代入
                $lists[$i]['application_status'] = "承認待ち";
            }
            if ($correction_work[$i]['application_status'] == 2) {
                // application_statusが2の時、[承認済み]を代入
                $lists[$i]['application_status'] = "承認済み";
            }
            // 名前
            $lists[$i]['name'] = User::find($correction_work[$i]['user_id'])->name;
            // 対象日時
            $lists[$i]['attendance_time'] = \Carbon\Carbon::parse($correction_work[$i]['attendance_time'])->format('Y/m/d');
            // 申請理由
            $lists[$i]['remarks'] = $correction_work[$i]['remarks'];
            // 申請日時
            $lists[$i]['application_date'] = \Carbon\Carbon::parse($correction_work[$i]['application_date'])->format('Y/m/d');
        }
        return $lists;
    }
}
