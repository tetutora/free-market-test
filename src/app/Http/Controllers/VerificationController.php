<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Log をインポート
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify'); // 署名付きリンクの処理
        $this->middleware('throttle:6,1')->only('verify', 'resend'); // リクエストの速さ制御
    }

    public function verify(Request $request, $id, $hash)
    {
        Log::info("Verification process started for user ID: {$id}");

        $user = User::find($id);

        if ($user && hash_equals($hash, sha1($user->email))) {
            Log::info("Verification successful for user ID: {$id}");
            $user->markEmailAsVerified();
            return redirect()->intended('/')->with('status', 'Email successfully verified!');
        }

        Log::warning("Verification failed for user ID: {$id}");
        return redirect()->route('verification.notice')->with('error', 'Invalid signature');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return back()->with('status', '認証リンクを再送しました!');
        }

        return back()->withErrors(['error' => '認証リンクの再送ができませんでした。']);
    }

    public function show()
    {
        return view('auth.verify-email');
    }
}
