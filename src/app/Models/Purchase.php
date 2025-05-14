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
        return $this->belongsTo(Product::class)->with('user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

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

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
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

    public function getUnreadMessagesCountForUser(int $userId): int
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->count();
    }
}