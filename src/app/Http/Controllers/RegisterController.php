<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'このメールアドレスは既に登録されています。']);
        }

        // ユーザー登録
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // メール送信処理
        try {
            Mail::to($user->email)->send(new VerifyEmail($user));
        } catch (\Exception $e) {
            \Log::error('メール送信エラー: ' . $e->getMessage());
        }

        // ログイン処理
        Auth::login($user);

        // 管理画面にリダイレクト
        return redirect()->route('profile');
    }
}
