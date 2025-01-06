<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth; // 正しいインポート

class PurchaseController extends Controller
{
    public function show(Product $product)
    {
        return view('products.purchase', compact('product'));
    }

    public function purchase($productId)
    {
        $product = Product::findOrFail($productId);
        $user = Auth::user();
        $profile = $user->profile;

        // 必要なデータを取得
        $zipcode = $profile->zipcode ?? '未登録';
        $address = $profile->address ?? '未登録';
        $building = $profile->building ?? '未登録';

        return view('products.purchase', compact('product', 'zipcode', 'address', 'building'));
    }

    public function complete(Product $product)
    {
        // 購入処理を行うコードをここに追加
        // 支払い情報、配送先情報などを保存

        return redirect()->route('home');
    }
}
