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
    // 会員登録画面
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // 会員登録処理
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
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
}
