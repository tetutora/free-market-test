<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'seller_id', 'product_id', 'status',
    ];

    /**
     * ユーザーとのリレーションを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 売り手とのリレーションを取得
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 商品とのリレーションを取得
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->with('user');
    }

    /**
     * メッセージとのリレーションを取得
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * レーティングとのリレーションを取得
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function isBuyerRated(): bool
    {
        return $this->ratings()->where('user_id', $this->user_id)->exists();
    }

    public function isSellerRated(): bool
    {
        return $this->ratings()->where('user_id', $this->product->user_id)->exists();
    }


    /**
     * 新しい取引を記録
     */
    public static function record(User $user, Product $product): void
    {
        self::create([
            'user_id' => $user->id,
            'seller_id' => $product->user_id,
            'product_id' => $product->id,
            'status' => 'trading',
        ]);
    }

    /**
     * 未読メッセージをユーザーに対してマークする
     */
    public function markMessagesAsReadForUser(int $userId)
    {
        $this->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->each(function ($message) {
                $message->is_read = true;
                $message->save();
            });
    }

    /**
     * 他の取引を取得
     */
    public function getOtherTransactions(User $user)
    {
        return Purchase::where('status', 'trading')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->where('id', '!=', $this->id)
            ->with('product')
            ->get();
    }
}