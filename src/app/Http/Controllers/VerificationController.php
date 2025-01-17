<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // メール認証を完了する
    public function verify(EmailVerificationRequest $request)
    {
        try {
            $request->fulfill();
            Log::info("Verification successful for user ID: " . $request->user()->id);
            return redirect('/')->with('status', 'Email successfully verified!');
        } catch (\Exception $e) {
            Log::error("Verification failed: " . $e->getMessage());
            return redirect()->route('verification.notice')->with('error', 'Verification failed.');
        }
    }

    // 認証メールを再送する
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return back()->with('status', '認証リンクを再送しました!');
        }

        return back()->withErrors(['error' => '認証リンクの再送ができませんでした。']);
    }

    // 認証待ち画面を表示する
    public function show()
    {
        return view('auth.verify-email');
    }
}
