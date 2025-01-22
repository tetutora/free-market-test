<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->mustBeVerified()) {
                Auth::logout();
                return redirect()->route('verification.notice')->withErrors(['email' => 'メール認証が完了していません。認証リンクを確認してください。']);
            }

            return redirect()->route('profile.edit');
        }

        return redirect()->back()->withErrors(['email' => 'ログイン情報が登録されていません。']);
    }
}