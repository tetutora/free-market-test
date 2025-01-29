<?

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new VerifyEmail($signedUrl));

        Auth::login($user);

        return redirect()->route('verification.notice')->with('status', '登録完了しました。認証メールを確認してください。');
    }

    // public function register(RegisterRequest $request)
    // {
    //     if (User::where('email', $request->email)->exists()) {
    //         return redirect()->back()->withErrors(['email' => 'このメールアドレスはすでに登録されています。']);
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     try {
    //         Mail::to($user->email)->send(new VerifyEmail($user));
    //     } catch (\Exception $e) {
    //         \Log::error('メール送信エラー: ' . $e->getMessage());
    //         return redirect()->back()->withErrors(['email' => 'メールの送信に失敗しました。時間をおいて再度お試しください。']);
    //     }

    //     return redirect()->route('verification.notice')->with('status', '登録が完了しました。認証メールをご確認ください。');
    // }

    // public function register(RegisterRequest $request)
    // {
    //     if (User::where('email', $request->email)->exists()) {
    //         return redirect()->back()->withErrors(['email' => 'このメールアドレスはすでに登録されています。']);
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'email_verification_hash' => sha1($request->email), // ハッシュを保存
    //     ]);

    //     $signedUrl = URL::temporarySignedRoute(
    //         'verification.verify',
    //         now()->addMinutes(60),
    //         ['id' => $user->id, 'hash' => sha1($user->email)]
    //     );

    //     try {
    //         Mail::to($user->email)->send(new VerifyEmail($signedUrl));
    //     } catch (\Exception $e) {
    //         \Log::error('メール送信エラー: ' . $e->getMessage());
    //         return redirect()->back()->withErrors(['email' => 'メールの送信に失敗しました。時間をおいて再度お試しください。']);
    //     }

    //     return redirect()->route('verification.notice')->with('status', '登録が完了しました。認証メールをご確認ください。');
    // }
}
