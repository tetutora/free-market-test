<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function record(User $user, Product $product): void
    {
        $profile = $user->profile;

        self::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            [
                'zipcode'  => $profile->zipcode ?? '未登録',
                'address'  => $profile->address ?? '未登録',
                'building' => $profile->building ?? '未登録',
            ]
        );
    }
}
