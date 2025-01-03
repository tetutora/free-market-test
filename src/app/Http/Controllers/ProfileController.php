<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AddressRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function myPage()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $user->profile;

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->name = $user->name;
            $profile->save();
        }

        $profile_picture = $profile->profile_picture
            ? asset('storage/' . $profile->profile_picture)
            : asset('images/default-profile.jpg');

        return view('profile.mypage', [
            'user' => $user,
            'profile' => $profile,
            'profile_picture' => $profile_picture,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;

        // プロフィール画像の保存処理
        if ($request->hasFile('profile_picture')) {
            // 古い画像があれば削除
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }
            // 新しい画像を保存
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('profile.mypage');
    }


    // 住所編集フォームを表示
    public function editAddress()
    {
        $user = Auth::user();
        return view('profile.address.edit', compact('user'));
    }

    // 住所を更新
    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        // 住所の更新
        $user->address->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
        ]);

        return redirect()->route('mypage')->with('success', '住所が更新されました');
    }

    public function edit()
    {
        $user = auth()->user();

        // プロフィールが存在しない場合、新しいプロフィールを作成
        $profile = $user->profile;
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();  // プロフィールを保存
        }

        // プロフィール画像のパスを取得
        $profile_picture = $profile->profile_picture 
            ? asset('storage/' . $profile->profile_picture) 
            : asset('images/default-profile.png');  // デフォルト画像を設定

        return view('profile.edit', compact('user', 'profile', 'profile_picture'));
    }

    public function update(AddressRequest $request)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // プロフィールが存在しない場合、新しいプロフィールを作成
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->name = $user->name;
            $profile->zipcode = $request->input('zipcode');
            $profile->address = $request->input('address');
            $profile->building = $request->input('building');
            $profile->save();
        }

        // プロフィール画像がアップロードされていれば保存
        if ($request->hasFile('profile_picture')) {
            $filePath = $request->file('profile_picture')->store('profiles', 'public');
            $profile->profile_picture = $filePath;
        }

        // 他のフィールドを更新
        $profile->name = $request->input('name');
        $profile->zipcode = $request->input('zipcode');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        $profile->save();

        return redirect()->route('profile.mypage');
    }
}
