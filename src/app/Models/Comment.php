<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'content'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function postFromRequest(Product $product, int $userId, string $content): self{
        return self::crerate([
            'product_id' => $product->id,
            'user_id => $userId',
            'content' => $content,
        ]);
    }
}
