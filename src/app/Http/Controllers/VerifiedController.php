<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifiedController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        if (!$user['email_verified_at']) {
            // メール認証していなければメール認証画面へ
            return view('auth.verify-email');
        } else {
            // メール認証していれば勤怠登録画面へ
            return redirect('attendance');
        }
    }
}
