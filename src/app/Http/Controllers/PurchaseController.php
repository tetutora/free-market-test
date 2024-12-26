<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function show(Product $product)
    {
        return view('products.purchase', compact('product'));
    }

    public function complete(Product $product)
    {
        // 購入処理を行うコードをここに追加
        // 支払い情報、配送先情報などを保存

        return redirect()->route('home');
    }

}
