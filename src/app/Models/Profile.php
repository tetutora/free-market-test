<?php

namespace App\Models;

use App\Models\Message;
use App\Models\Purchase;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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

    public function getMyPageData()
    {
        $user = $this->user;

        $profile_picture = $this->profile_picture
            ? asset('storage/' . $this->profile_picture)
            : asset('images/default-profile.jpg');

        $purchasedProducts = $user->purchases()
            ->where('status', 'completed')
            ->with('product')
            ->get();

        $allTradingProducts = $this->getAllTradingProductsWithUnreadMessages($user);

        foreach ($allTradingProducts as $purchase) {
            $purchase->unread_messages_count = $purchase->messages()
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->count();
        }

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

        return compact(
            'user',
            'profile_picture',
            'purchasedProducts',
            'allTradingProducts',
            'unreadMessageCount',
            'averageRatingRounded'
        );
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

    public function getAllTradingProductsWithUnreadMessages($user)
    {
        $tradingAsBuyer = Purchase::where('user_id', $user->id)
            ->where('status', 'trading')
            ->with('product', 'messages')
            ->get();

        $tradingAsSeller = Purchase::where('seller_id', $user->id)
            ->where('status', 'trading')
            ->with('product', 'messages')
            ->get();

        $allTradingProducts = $tradingAsBuyer->merge($tradingAsSeller);

        return $allTradingProducts->map(function ($purchase) {
            $purchase->unread_messages_count = $purchase->messages->where('is_read', false)->count();

            return $purchase;
        })->sortByDesc(function ($purchase) {
            $unreadMessage = $purchase->messages->where('is_read', false)->sortByDesc('created_at')->first();

            if ($unreadMessage) {
                return $unreadMessage->created_at;
            }

            return $purchase->product->created_at;
        });
    }
}
