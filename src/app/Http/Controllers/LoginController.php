<?php

namespace App\Http\Controllers;

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
        $login = $request->input('email');
        $password = $request->input('password');

        $credentials = ['password' => $password];

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $login;
        } else {
            $credentials['name'] = $login;
        }

        // 認証を試行
        if (Auth::attempt($credentials)) {
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            return redirect()->route('products.index');
        }

        // 認証失敗時
        return redirect()->back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }
}

