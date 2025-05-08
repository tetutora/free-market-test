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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public static function record(User $user, Product $product): void
    {
        self::create([
            'user_id' => $user->id,
            'seller_id' => $product->user_id,
            'product_id' => $product->id,
            'status' => 'trading',
        ]);
    }
}