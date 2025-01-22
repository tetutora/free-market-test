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
    // プロフィール表示
    public function show()
    {
        return view('profile.show');
    }

    // マイページ表示
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
            $profile->save();
        }

        $profile_picture = $profile->profile_picture
            ? asset('storage/' . $profile->profile_picture)
            : asset('images/default-profile.jpg');

        $purchasedProducts = $user->purchasedProducts()->with('categories')->get();

        return view('profile.mypage', compact('user', 'profile', 'profile_picture', 'purchasedProducts'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->name = $request->input('name', $user->name);  // フォームから値が無い場合、デフォルトとしてユーザー名を挿入
            $profile->save();
        } else {
            if ($request->has('name')) {
                $profile->name = $request->input('name');
            } else {
                $profile->name = $user->name;  // ユーザー名をデフォルトとして挿入
            }

            // プロフィール画像の保存処理
            if ($request->hasFile('profile_picture')) {
                if ($profile->profile_picture && Storage::exists('public/' . $profile->profile_picture)) {
                    Storage::delete('public/' . $profile->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profiles', 'public');
                $profile->profile_picture = $path;
            }

            // プロフィール情報の更新
            $profile->zipcode = $request->input('zipcode');
            $profile->address = $request->input('address');
            $profile->building = $request->input('building');

            $profile->save();
        }

        return redirect()->route('profile.mypage');
    }

    // 住所編集画面
    public function editAddress(Request $request)
    {
        $profile = auth()->user()->profile;

        $productId = $request->query('productId');

        return view('profile.address.edit', compact('profile', 'productId'));
    }

    // 住所更新処理
    public function updateAddress(Request $request)
    {
        $profile = auth()->user()->profile;

        $profile->zipcode = $request->zipcode;
        $profile->address = $request->address;
        $profile->building = $request->building;

        $profile->save();

        return redirect()->route('purchase.show', ['productId' => $request->productId]); 
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = auth()->user();

        $profile = $user->profile;
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();  // プロフィールを保存
        }

        // プロフィール画像のパスを取得
        $profile_picture = $profile->profile_picture 
            ? asset('storage/' . $profile->profile_picture) 
            : asset('images/default-profile.png');

        return view('profile.edit', compact('user', 'profile', 'profile_picture'));
    }
}