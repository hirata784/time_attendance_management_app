<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::all();
        $lists = [];

        for ($i = 0; $i < count($users); $i++) {
            // id
            $lists[$i]['id'] = $i + 1;
            // 名前
            $lists[$i]['name'] = $users[$i]['name'];
            // メールアドレス
            $lists[$i]['email'] = $users[$i]['email'];
        }
        return view('admin/staff', compact('lists'));
    }
}
