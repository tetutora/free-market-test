<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 商品購入画面表示
    public function show($item_id) {
        $product = Product::find($item_id);

        if (!$product) {
            return abort(404, 'Product not found');
        }

        $user = Auth::user();
        $profile = $user->profile;

        $zipcode = $profile->zipcode ?? '未登録';
        $address = $profile->address ?? '未登録';
        $building = $profile->building ?? '未登録';

        return view('products.purchase', compact('product', 'zipcode', 'address', 'building', 'item_id'));
    }

    // 購入完了処理
    public function complete($item_id) {
        $product = Product::findOrFail($item_id);

        if (!$product) {
            return redirect()->route('home')->with('error', 'Product not found');
        }

        $product->is_sold = true;
        $product->save();

        $user = Auth::user();
        $user->purchasedProducts()->attach($item_id);

        return redirect()->route('profile.mypage')->with('success', 'Purchase complete');
    }
}
