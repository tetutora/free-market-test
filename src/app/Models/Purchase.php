<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'buyer_id', 'product_id', 'status',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
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

    public static function handlePurchase(User $user, string $sessionId, string $paymentMethod): array
    {
        $result = (new Product)->handlePurchaseSession($sessionId, $paymentMethod);

        if ($result['success']) {
            $product = Product::where('session_id', $sessionId)->first();

            $purchase = new self([
                'buyer_id' => $user->id,
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'status' => 'trading',
            ]);
            $purchase->save();

            return ['success' => true, 'message' => '購入が完了しました。'];
        }

        return ['success' => false, 'message' => '購入に失敗しました。'];
    }
}
