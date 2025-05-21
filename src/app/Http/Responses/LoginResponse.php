<?php

namespace App\Http\Responses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // メール認証しているかチェック
        $user_id = Auth::id();
        $user = User::find($user_id);

        if (!$user['email_verified_at']) {
            // メール認証していなければメール認証画面へ
            $home = 'verified';
            return redirect($home);
        } else {
            // メール認証していれば勤怠登録画面へ
            $home = '/attendance';
            return redirect($home);
        }
    }
}
