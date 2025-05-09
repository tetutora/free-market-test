<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'profile_picture', 'name', 'zipcode', 'address', 'building'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateOrCreateForUser($user, $data, $file = null)
    {
        $profile = $user->profile ?? new self(['user_id' => $user->id]);

        $profile->fill([
            'name' => $data['name'] ?? $user->name,
            'zipcode' => $data['zipcode'] ?? '',
            'address' => $data['address'] ?? '',
            'building' => $data['building'] ?? '',
        ]);

        if ($file) {
            if ($profile->profile_picture && Storage::exists('public/' . $profile->profile_picture)) {
                Storage::delete('public/' . $profile->profile_picture);
            }
            $profile->profile_picture = $file->store('profiles', 'public');
        }

        $profile->save();

        return $profile;
    }

    public function updateAddress(array $data)
    {
        $this->fill([
            'zipcode' => $data['zipcode'] ?? '',
            'address' => $data['address'] ?? '',
            'building' => $data['building'] ?? '',
        ]);

        $this->save();
    }

    /**
     * 取引中の商品と未読メッセージ数を新しい順に並べたリストを取得
     *
     * @param $user
     * @return \Illuminate\Support\Collection
     */
    public function getAllTradingProductsWithUnreadMessages($user)
    {
        // 購入者としての取引を取得
        $tradingAsBuyer = \App\Models\Purchase::where('user_id', $user->id)
                                ->where('status', 'trading')
                                ->with('product', 'messages')
                                ->get();

        // 販売者としての取引を取得
        $tradingAsSeller = \App\Models\Purchase::where('seller_id', $user->id)
                                ->where('status', 'trading')
                                ->with('product', 'messages')
                                ->get();

        // 購入者としてと販売者としての取引を結合
        $allTradingProducts = $tradingAsBuyer->merge($tradingAsSeller);

        // 未読メッセージが新しい順に並べる
        return $allTradingProducts->sortByDesc(function ($purchase) {
            $unreadMessage = $purchase->messages->where('is_read', false)->sortByDesc('created_at')->first();

            if ($unreadMessage) {
                return $unreadMessage->created_at;
            }

            return $purchase->product->created_at;
        });
    }
}
