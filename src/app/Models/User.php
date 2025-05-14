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

    public function sales()
    {
        return $this->hasMany(Product::class, 'user_id');
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function productCategories()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'user_id', 'product_id');
    }

    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'purchases');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function verifyEmail()
    {
        if (!$this->hasVerifiedEmail()) {
            $this->markEmailAsVerified();
        }
    }

    public function attachPurchasedProduct(Product $product)
    {
        $this->purchasedProducts()->syncWithoutDetaching([$product->id]);
    }

    public function getPurchasedProductsWithDetails()
    {
        return $this->purchasedProducts()
            ->with(['purchase', 'product'])
            ->get();
    }

    public function getSellingProducts()
    {
        return $this->sales()->with('product')->get();
    }

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

    public function getPurchaseHistory()
    {
        return $this->purchases()->with('product')->get();
    }

    public function resendVerificationEmail()
    {
        $this->sendEmailVerificationNotification();
    }
}
