<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 商品とのリレーション
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_categories');
    }
}
