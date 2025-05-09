<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * プロフィール設定画面表示
     */
    public function show()
    {
        return view('profile.show');
    }

    /**
     * マイページ画面表示
     */
    public function myPage()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $user->profile ?: new Profile(['user_id' => $user->id]);
        $profile_picture = $profile->profile_picture ? asset('storage/' . $profile->profile_picture) : asset('images/default-profile.jpg');

        $purchasedProducts = $user->purchases()
                            ->where('status', 'completed')
                            ->with('product')
                            ->get();
                        // dd($purchasedProducts);

        $allTradingProducts = $profile->getAllTradingProductsWithUnreadMessages($user);

        $unreadMessageCount = Message::whereHas('purchase', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })->where('sender_id', '!=', $user->id)
        ->where('is_read', false)
        ->count();

        $averageRating = Rating::whereHas('purchase', function ($query) use ($user) {
            $query->where('seller_id', $user->id);
        })->avg('rating');

        $averageRatingRounded = $averageRating ? round($averageRating) : null;

        return view('profile.mypage', compact(
            'user',
            'profile',
            'profile_picture',
            'unreadMessageCount',
            'purchasedProducts',
            'allTradingProducts',
            'averageRatingRounded'
        ));
    }

    /**
     * プロフィール編集画面表示
     */
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile ?: new Profile(['user_id' => $user->id]);

        $profile_picture = $profile->profile_picture
            ? asset('storage/' . $profile->profile_picture)
            : asset('images/default-profile.jpg');

        return view('profile.edit', compact('user', 'profile', 'profile_picture'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(AddressRequest $request)
    {
        $user = auth()->user();

        Profile::updateOrCreateForUser($user, $request->only(['name', 'zipcode', 'address', 'building']), $request->file('profile_picture'));

        return redirect()->route('profile.mypage');
    }

    /**
     * 購入画面からの住所編集画面表示
     */
    public function editAddress(Request $request, $item_id)
    {
        $profile = auth()->user()->profile;

        return view('profile.address.edit', compact('profile', 'item_id'));
    }

    /**
     * 購入画面からの住所更新処理
     */
    public function updateAddress(Request $request, $item_id)
    {
        $profile = auth()->user()->profile;

        $profile->updateAddress($request->only(['zipcode', 'address', 'building']));

        return redirect()->route('products.purchase', ['item_id' => $item_id]);
    }
}