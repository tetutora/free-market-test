<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    public function purchases()
    {
        return $this->hasManyThrough(Product::class, Purchase::class, 'user_id', 'id', 'id', 'product_id');
    }


    // プロフィールとの1対1リレーション
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }
}
