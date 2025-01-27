<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;



class VerificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

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