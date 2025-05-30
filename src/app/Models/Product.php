<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\PaymentIntent;



class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'brand_name', 'description', 'price', 'status', 'image', 'user_id', 'is_sold',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'products_categories', 'product_id', 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchases', 'product_id', 'user_id')
                    ->withTimestamps();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function isSold()
    {
        return $this->purchases()->exists();
    }

    public function getIsSoldAttribute()
    {
        return $this->purchases()->exists();
    }

    public static function searchExcludingUser(?string $keyword, ?int $userId)
    {
        return self::query()
            ->when($keyword, fn($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->when($userId, fn($q) => $q->where('user_id', '!=', $userId))
            ->get();
    }

    public static function createFromRequest($request)
    {
        $imagePath = $request->hasfile('image') ? $request->file('image')->store('products', 'public') : null;

        $product = self::create([
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'description' => $request->description,
            'price' => $request->price,
            'status' => $request->status,
            'user_id' => Auth::id(),
            'image' => $imagePath,
        ]);

        $categoryIds = explode(',', $request->category_id);
        $product->categories()->attach($categoryIds, ['user_id' => Auth::id()]);

        return $product;
    }

    public function toggleFavoriteByUser($user)
    {
        $user->favorites()->toggle($this->id);
        return [
            'favorited' => $user->favorites()->where('product_id', $this->id)->exists(),
            'favoriteCount' => $this->favorites()->count(),
        ];
    }

    public static function handlePurchaseSession($sessionId, $paymentMethod = null): array
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

            if ($paymentIntent->status !== 'succeeded') {
                return ['success' => false, 'message' => '支払いが完了していません。'];
            }

            $item_id = $session->metadata->item_id;
            $product = self::find($item_id);
            if (!$product) {
                return ['success' => false, 'message' => '商品が見つかりませんでした'];
            }

            if ($product->is_sold) {
                return ['success' => false, 'message' => 'この商品はすでに購入済みです。'];
            }

            $product->markAsSold();

            $user = auth()->user();

            Purchase::record($user, $product);

            session()->put('payment_method', $paymentMethod);

            return ['success' => true, 'message' => '購入が完了しました'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => '購入処理中にエラーが発生しました'];
        }
    }

    public function markAsSold()
    {
        $this->update(['is_sold' => true]);
    }

    public static function createStripeSession($productId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $product = self::find($productId);

        if (!$product) {
            return ['error' => '指定された商品が見つかりません。'];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $product->name,
                                'description' => $product->description,
                            ],
                            'unit_amount' => $product->price * 100,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('purchase.success'),
                'cancel_url' => route('purchase.cancel'),
                'metadata' => ['item_id' => $productId],
            ]);

            return $session;
        } catch (\Exception $e) {
            return ['error' => 'セッション作成中にエラーが発生しました: ' . $e->getMessage()];
        }
    }
}
