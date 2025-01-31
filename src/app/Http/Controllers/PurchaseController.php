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
    // public function complete($item_id) {
    //     $product = Product::findOrFail($item_id);

    //     if (!$product) {
    //         return redirect()->route('home')->with('error', 'Product not found');
    //     }

    //     $product->is_sold = true;
    //     $product->save();

    //     $user = Auth::user();
    //     $user->purchasedProducts()->attach($item_id);

    //     return redirect()->route('profile.mypage')->with('success', 'Purchase complete');
    // }

    // 購入成功画面
    // public function success(Request $request)
    // {
    //     // クエリパラメータからセッションIDを取得
    //     $sessionId = $request->query('session_id');

    //     if (!$sessionId) {
    //         return redirect()->route('profile.mypage')->with('error', '無効なセッションID');
    //     }

    //     // Stripe でセッションを検証
    //     try {
    //         $session = \Stripe\Checkout\Session::retrieve($sessionId);

    //         if ($session->payment_status == 'paid') {
    //             return redirect()->route('profile.mypage')->with('success', '購入が完了しました');
    //         } else {
    //             return redirect()->route('profile.mypage')->with('error', '支払いに失敗しました');
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->route('profile.mypage')->with('error', 'セッションの検証中にエラーが発生しました');
    //     }
    // }

    public function success(Request $request, $item_id)
    {
        $product = Product::findOrFail($item_id);
        // クエリパラメータからセッションIDを取得
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('profile.mypage')->with('error', '無効なセッションID');
        }

        // Stripeでセッションを検証
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // 支払いが成功している場合
            if ($session->payment_status == 'paid') {

                $item_id = $session->metadata->item_id;

                // 商品を取得
                $product = Product::find($item_id);

                if (!$product) {
                    return redirect()->route('profile.mypage')->with('error', '商品が見つかりませんでした');
                }

                // 商品の販売状態を更新
                $product->is_sold = true;
                $product->save();

                // 購入した商品をユーザーに関連付け
                $user = Auth::user();
                $user->purchasedProducts()->attach($item_id);

                // 購入情報をpurchasesテーブルに保存
                \App\Models\Purchase::create([
                    'user_id' => $user->id,
                    'product_id' => $item_id,
                ]);

                return redirect()->route('profile.mypage')->with('success', '購入が完了しました');
            } else {
                return redirect()->route('profile.mypage')->with('error', '支払いに失敗しました');
            }
        } catch (\Exception $e) {
            return redirect()->route('profile.mypage')->with('error', 'セッションの検証中にエラーが発生しました');
        }
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }
}
