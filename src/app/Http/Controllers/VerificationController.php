<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * メール認証処理
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->hasValidSignature()) {
            $user = $request->user();
            $user->verifyEmail();
            Auth::login($user);

            return redirect()->route('profile.show');
        } else {
            return redirect()->route('verification.notice')->withErrors(['error' => '認証リンクが無効です。']);
        }
    }

    /**
     * 認証メール再送処理
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->resendVerificationEmail();
            return back()->with('status', '認証リンクを再送しました!');
        }

        return back()->withErrors(['error' => '認証リンクの再送ができませんでした。']);
    }

    /**
     * メール認証催促画面表示
     */
    public function show()
    {
        return view('auth.verify-email');
    }
}