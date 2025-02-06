<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // メール認証処理
    public function verify(EmailVerificationRequest $request)
    {
        if($request->hasValidSignature()){
            $user = $request->user();

            if(!$user->hasVerifiedEmail()){
                $user->markEmailAsVerified();
            }

            Auth::login($user);

            return redirect()->route('profile.show');
            } else {
                return redirect()->route('verification.notice')->withErrors(['error' => '認証リンクが無効です。']);
            }
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail()) {

            $request->validate([
                '_token' => 'required|csrf',
            ]);

            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            Mail::to($user->email)->send(new VerifyEmail($signedUrl));

            return back()->with('status', '認証リンクを再送しました!');
        }

        return back()->withErrors(['error' => '認証リンクの再送ができませんでした。']);
    }

    public function show()
    {
        return view('auth.verify-email');
    }
}
