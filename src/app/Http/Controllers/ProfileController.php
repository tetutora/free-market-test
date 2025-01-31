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

    // プロフィール更新処理
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('profile.mypage');
    }

    // 住所編集画面
    public function editAddress(Request $request, $item_id)
    {
        $profile = auth()->user()->profile;

        return view('profile.address.edit', compact('profile', 'item_id'));
    }

    // 住所更新処理
    public function updateAddress(Request $request, $item_id)
    {
        $profile = auth()->user()->profile;

        $profile->zipcode = $request->zipcode;
        $profile->address = $request->address;
        $profile->building = $request->building;

        $profile->save();

        return redirect()->route('products.purchase', ['item_id' => $item_id]);
    }

    // プロフィール編集画面
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
            : asset('images/default-profile.png');

        return view('profile.edit', compact('user', 'profile', 'profile_picture'));
    }

    // プロフィール更新処理
    public function update(AddressRequest $request)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->name = $user->name;
            $profile->zipcode = $request->input('zipcode');
            $profile->address = $request->input('address');
            $profile->building = $request->input('building');
            $profile->save();
        }

        if ($request->hasFile('profile_picture')) {
            $filePath = $request->file('profile_picture')->store('profiles', 'public');
            $profile->profile_picture = $filePath;
        }

        $profile->name = $request->input('name');
        $profile->zipcode = $request->input('zipcode');
        $profile->address = $request->input('address');
        $profile->building = $request->input('building');

        $profile->save();

        return redirect()->route('profile.mypage');
    }
}
