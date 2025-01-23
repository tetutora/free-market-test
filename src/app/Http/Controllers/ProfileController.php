<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AddressRequest;

class ProfileController extends Controller
{
    // プロフィール設定ページの表示
    public function show()
    {
        return view('profile.show');
    }

    // プロフィール設定の処理
    public function update(AddressRequest $request)
    {
        $user = Auth::user();

        $profile = $user->profile ?? new Profile();

        $profile->name = $request->input('name');
        $profile->address = $request->input('address');
        $profile->zipcode = $request->input('zipcode');
        $profile->building = $request->input('building');

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $profile->profile_picture = $path;
        }

        $profile->user_id = $user->id;

        $profile->save();

        return redirect()->route('profile.mypage');
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

    // マイページのプロフィール編集ボタンクリック後のプロフィール設定ページ
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();
        }

        $profile_picture = $profile->profile_picture
            ? asset('storage/' . $profile->profile_picture)
            : asset('images/default-profile.jpg');

        return view('profile.edit', compact('user', 'profile', 'profile_picture'));
    }

    // 住所編集画面
    public function editAddress(Request $request, $productId)
    {
        $profile = auth()->user()->profile;
        $product = Product::find($request->productId);

        return view('profile.address.edit', compact('profile','productId'));
    }

    // 住所更新処理
    public function updateAddress(Request $request, $productId)
    {
        $profile = auth()->user()->profile;
        $product = Product::find($request->productId);

        $profile->zipcode = $request->zipcode;
        $profile->address = $request->address;
        $profile->building = $request->building;

        $profile->save();

        return redirect()->route('profile.mypage');
    }
}