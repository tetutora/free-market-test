<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth; // 正しいインポート

class PurchaseController extends Controller
{
    public function show($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return abort(404, 'Product not found');
        }

        $user = Auth::user();
        $profile = $user->profile;

        // 必要なデータを取得
        $zipcode = $profile->zipcode ?? '未登録';
        $address = $profile->address ?? '未登録';
        $building = $profile->building ?? '未登録';

        return view('products.purchase', compact('product', 'zipcode', 'address', 'building', 'productId'));
    }

    public function complete($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product) {
            return redirect()->route('home')->with('error', 'Product not found');
        }

        $product->is_sold = true;
        $product->save();

        // 購入済み商品の情報をユーザーのマイページに追加
        $user = Auth::user();
        $user->purchasedProducts()->attach($productId);  // マイページに購入済みの商品を追加

        return redirect()->route('profile.mypage')->with('success', 'Purchase complete');
    }

    public function myPurchases()
{
    $user = Auth::user();
    $purchasedProducts = $user->purchases()->get();

    return view('products.my-purchases', compact('purchasedProducts'));
}

}
