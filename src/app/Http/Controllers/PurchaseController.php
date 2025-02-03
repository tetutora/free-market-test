<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        // 商品情報を取得
        $product = Product::find($item_id);

        // 商品が見つからない場合
        if (!$product) {
            return abort(404, 'Product not found');
        }

        // ユーザー情報を取得
        $user = Auth::user();
        $profile = $user->profile;

        $zipcode = $profile->zipcode ?? '未登録';
        $address = $profile->address ?? '未登録';
        $building = $profile->building ?? '未登録';

        return view('products.purchase', compact('product', 'zipcode', 'address', 'building', 'item_id'));
    }

    // public function success(Request $request)
    // {
    //     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    //     // クエリパラメータからセッションIDを取得
    //     $sessionId = $request->query('session_id');

    //     // セッションIDが無い場合
    //     if (!$sessionId) {
    //         return redirect()->route('profile.mypage')->with('error', '無効なセッションID');
    //     }

    //     // Stripeでセッションを検証
    //     try {
    //         $session = \Stripe\Checkout\Session::retrieve($sessionId);

    //         // セッション情報をログに出力（デバッグ用）
    //         Log::info('Stripe Session: ', (array) $session);  // セッション全体をログに出力

    //         // メタデータからitem_idを取得
    //         $item_id = $session->metadata->item_id;
    //         Log::info('Item ID from Stripe session: ' . $item_id);  // item_idをログに出力

    //         // 商品を取得
    //         $product = Product::find($item_id);
    //         if (!$product) {
    //             Log::error('商品が見つかりません: ' . $item_id);
    //             return redirect()->route('profile.mypage')->with('error', '商品が見つかりませんでした');
    //         }

    //         // 商品の販売状態を更新
    //         $product->is_sold = true;
    //         $product->save();

    //         // ユーザーに購入した商品を関連付け
    //         $user = Auth::user();
    //         $user->purchasedProducts()->attach($item_id);

    //         // 購入情報をpurchasesテーブルに保存
    //         \App\Models\Purchase::create([
    //             'user_id' => $user->id,
    //             'product_id' => $item_id,
    //         ]);
    //         Log::info('Purchase saved: user_id=' . $user->id . ', product_id=' . $item_id);

    //         return redirect()->route('profile.mypage')->with('success', '購入が完了しました');
    //     } catch (\Exception $e) {
    //         Log::error('Purchase save error: ' . $e->getMessage());
    //         return redirect()->route('profile.mypage')->with('error', '購入情報の保存に失敗しました');
    //     }
    // }

    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('profile.mypage')->with('error', '無効なセッションID');
        }

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $paymentIntentId = $session->payment_intent;
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status !== 'succeeded') {
                return redirect()->route('profile.mypage')->with('error', '支払いが完了していません。');
            }

            $item_id = $session->metadata->item_id;
            $product = Product::find($item_id);

            if (!$product) {
                return redirect()->route('profile.mypage')->with('error', '商品が見つかりませんでした');
            }

            // 二重購入の防止
            if ($product->is_sold) {
                return redirect()->route('profile.mypage')->with('error', 'この商品はすでに購入済みです。');
            }

            $product->is_sold = true;
            $product->save();

            $user = Auth::user();
            $user->purchasedProducts()->syncWithoutDetaching([$item_id]);

            \App\Models\Purchase::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $item_id,
            ]);

            return redirect()->route('profile.mypage')->with('success', '購入が完了しました');
        } catch (\Exception $e) {
            Log::error('Purchase save error: ' . $e->getMessage());
            return redirect()->route('profile.mypage')->with('error', '購入情報の保存に失敗しました');
        }
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }
}
