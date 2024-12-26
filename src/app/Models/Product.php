<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'image', 'status_id'
    ];

    // カテゴリとの多対多リレーション
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    // ステータスとのリレーション
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
