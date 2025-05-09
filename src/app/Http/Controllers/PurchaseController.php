<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面表示
     */
    public function show($item_id)
    {
        $product = Product::find($item_id);
        if (!$product) abort(404, 'Product not found');

        $profile = Auth::user()->profile;

        return view('products.purchase', [
            'product' => $product,
            'zipcode' => $profile->zipcode ?? '未登録',
            'address' => $profile->address ?? '未登録',
            'building' => $profile->building ?? '未登録',
            'item_id' => $item_id,
        ]);
    }

    /**
     * 商品購入処理
     */
    public function purchase(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('home')->with('error', '無効なセッションID');
        }

        $result = (new Product)->handlePurchaseSession($sessionId, $request->payment_method);

        $route = $result['success'] ? 'home' : 'profile.mypage';
        return redirect()->route($route)->with($result['success'] ? ['success' => $result['message']] : ['error' => $result['message']]);
    }

    /**
     * 購入キャンセル処理
     */
    public function cancel()
    {
        return view('purchase.cancel');
    }
}
