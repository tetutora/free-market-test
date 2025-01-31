<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;


class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        // 送信されたJSONを取得
        $paymentMethod = $request->input('payment_method');
        $item_id = $request->input('item_id');

        // Stripeの秘密鍵を設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 商品情報を取得
        $product = Product::find($item_id);

        // 商品が存在しない場合はエラーを返す
        if (!$product) {
            \Log::error('Product not found for item_id: ' . $item_id);
            return response()->json(['error' => '商品が見つかりません'], 404);
        }

        // 商品名と価格を取得
        $productName = $product->name;  // 商品名
        $productPrice = $product->price * 1;

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $productName,
                        ],
                        'unit_amount' => $productPrice,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success') . '?session_id=' . '{CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel'),
                'metadata' => [
                    'item_id' => $item_id,
                ],
            ]);

            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
