<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        // 入力値の取得
        $login = $request->input('email'); // ユーザー名またはメールアドレス
        $password = $request->input('password');

        // 認証条件を準備
        $credentials = ['password' => $password];

        // 入力がメールアドレスかどうかを判定して認証条件を変更
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $login;
        } else {
            $credentials['name'] = $login;
        }

        // 認証を試行
        if (Auth::attempt($credentials)) {
            // 認証成功時にリダイレクト
            return redirect()->intended('/');
        }

        // 認証失敗時
        return redirect()->back()->withErrors(['email' => 'ユーザー名またはメールアドレス、もしくはパスワードが正しくありません。']);
    }
}

