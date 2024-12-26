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
        $user = Auth::user(); // ログイン中のユーザーを取得
        $profile_picture = $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.jpg'); // プロフィール画像のパスを設定

        // 購入データを取得（リレーション）
        $purchases = $user->purchases()->with('product')->get();

        // ビューにデータを渡す
        return view('profile.mypage', compact('user', 'purchases', 'profile_picture'));
    }


    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->zipcode = $request->zipcode;
        $user->address = $request->address;
        $user->building = $request->building;

        // プロフィール画像の保存処理
        if ($request->hasFile('profile_picture')) {
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
        $user = Auth::user(); // ログイン中のユーザー情報を取得
        $imagePath = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
        return view('profile.edit', compact('user', 'imagePath'));
    }


    public function update(AddressRequest $request)
    {
        $user = Auth::user();

        // ユーザーに関連するプロフィールを取得（存在しない場合は新しいインスタンスを作成）
        $profile = $user->profile ?? new Profile();
        
        // プロフィールデータの更新
        $profile->name = $request->input('name');
        $profile->address = $request->input('address');
        $profile->zipcode = $request->input('zipcode');
        $profile->building = $request->input('building');

        // 画像のアップロード処理
        if ($request->hasFile('profile_picture')) {
            // 新しい画像を保存
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $profile->profile_picture = $path;
        }

        // ユーザーに関連付け
        $profile->user_id = $user->id;

        // プロフィールを保存
        $profile->save();

        return redirect()->route('profile.mypage');
    }



    public function editProfile()
    {
        $user = Auth::user(); // 認証済みユーザーを取得
        $imagePath = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
        return view('profile.mypage', compact('user', 'imagePath'));
    }
}
