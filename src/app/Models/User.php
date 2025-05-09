<?php

namespace App\Models;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'zipcode',
        'address',
        'building',
        'profile_picture',
        'email_verification_has',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ユーザーが販売した商品を取得
     */
    public function sales()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    /**
     * ユーザーのプロフィール情報を取得
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * ユーザーのお気に入り商品を取得
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }

    /**
     * ユーザーのお気に入り商品（タイムスタンプ付き）を取得
     */
    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    /**
     * ユーザーが関連する商品カテゴリーを取得
     */
    public function productCategories()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'user_id', 'product_id');
    }

    /**
     * ユーザーが購入した商品を取得
     */
    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'purchases');
    }

    /**
     * メール認証の通知を送信
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    /**
     * ユーザーの購入履歴を取得
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * メールアドレスを確認する
     */
    public function verifyEmail()
    {
        if (!$this->hasVerifiedEmail()) {
            $this->markEmailAsVerified();
        }
    }

    /**
     * 購入した商品を関連付ける
     */
    public function attachPurchasedProduct(Product $product)
    {
        $this->purchasedProducts()->syncWithoutDetaching([$product->id]);
    }

    /**
     * ユーザーが購入した商品を取得
     */
    public function getPurchasedProductsWithDetails()
    {
        return $this->purchasedProducts()
            ->with(['purchase', 'product'])
            ->get();
    }

    /**
     * ユーザーが出品した商品を取得
     */
    public function getSellingProducts()
    {
        return $this->sales()->with('product')->get();
    }

    /**
     * 購入した商品の情報と取引中の商品を取得
     */
    public function getTradingProductsWithUnreadMessages()
    {
        return $this->purchases()
            ->with(['product', 'messages' => function ($query) {
                $query->where('is_read', false);
            }])
            ->whereHas('purchase', function ($query) {
                $query->where('status', 'trading');
            })
            ->get();
    }

    /**
     * 購入履歴を取得（履歴に関連するプロダクトも含む）
     */
    public function getPurchaseHistory()
    {
        return $this->purchases()->with('product')->get();
    }

    /**
     * メール認証リンクを再送
     */
    public function resendVerificationEmail()
    {
        $this->sendEmailVerificationNotification();
    }
}
