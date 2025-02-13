<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
