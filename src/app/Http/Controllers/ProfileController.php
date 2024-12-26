<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AddressRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function mypage()
    {
        return view('profile.mypage');
    }

    public function updateProfile(AddressRequest $request)
    {
        $user = Auth()->user();

        // プロフィールデータの取得または新規作成
        $profile = $user->profile ?: new \App\Models\Profile;
        $profile->user_id = $user->id;
        $profile->name = $request->input('name');
        $profile->zipcode = $request->input('zipcode');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        // プロフィール画像の保存処理
        if ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture) {
                // 既存の画像を削除
                Storage::delete($profile->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures');
            $profile->profile_picture = $path;
        }

        // データベースに保存
        $profile->save();

        // トップページにリダイレクト
        return redirect()->route('home');
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
}
