<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\VerifyEmail;


class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify'); // 署名付きリンクの処理
        $this->middleware('throttle:6,1')->only('verify', 'resend'); // リクエストの速さ制御
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->fulfill()) {
            return redirect()->intended('/'); // 認証成功後リダイレクト先
        }

        return redirect()->route('verification.notice'); // 認証失敗時のリダイレクト先
    }

    public function resend()
    {
        request()->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link has been resent!');
    }

    public function show()
    {
        return view('auth.verify-email');
    }
}
