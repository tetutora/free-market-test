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
    // 商品購入ページ
    public function show($item_id)
    {
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

    // 商品購入処理
    public function purchase(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('home')->with('error', '無効なセッションID');
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

            if ($product->is_sold) {
                return redirect()->route('profile.mypage')->with('error', 'この商品はすでに購入済みです。');
            }

            $product->is_sold = true;
            $product->save();

            $user = Auth::user();
            $user->purchasedProducts()->syncWithoutDetaching([$item_id]);

            $profile = $user->profile;
            $zipcode  = $profile->zipcode ?? '未登録';
            $address  = $profile->address ?? '未登録';
            $building = $profile->building ?? '未登録';


            \App\Models\Purchase::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'product_id' => $item_id,
                ],
                [
                    'zipcode'  => $zipcode,
                    'address'  => $address,
                    'building' => $building,
                ]
            );

            session()->put('payment_method', $request->payment_method);

            Log::info("Purchase saved: User ID - {$user->id}, Product ID - {$item_id}");
            return redirect()->route('home')->with('success', '購入が完了しました');
        } catch (\Exception $e) {
            Log::error('Purchase save error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', '購入情報の保存に失敗しました');
        }
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }
}
