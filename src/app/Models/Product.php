<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'brand_name', 'description', 'price', 'status', 'image', 'user_id',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchasedBy()
    {
        return $this->belongsToMany(User::class, 'purchases', 'product_id', 'user_id')
            ->withTimestamps();
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
}
