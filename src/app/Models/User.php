<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\VerifyEmail;




class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        return $this->belongsToMany(Product::class, 'favorites');
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
}
