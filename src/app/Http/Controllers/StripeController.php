<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        // 送信されたデータの取得
        $paymentMethod = $request->input('payment_method');
        $item_id = $request->input('item_id');

        // StripeのAPIキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 商品情報を取得
        $product = Product::find($item_id);

        // 商品が見つからない場合
        if (!$product) {
            Log::error('商品が見つかりませんでした: item_id = ' . $item_id);
            return response()->json(['error' => '商品が見つかりません'], 404);
        }

        // 商品名と価格を取得
        $productName = $product->name;
        $productPrice = $product->price * 1; // Stripeは最小単位で金額を設定 (例えば、1000円なら1000)

        // 商品価格が正しいか確認
        if ($productPrice <= 0) {
            Log::error('無効な商品価格: ' . $productPrice);
            return response()->json(['error' => '商品価格が無効です'], 400);
        }

        try {
            // StripeのCheckoutセッションを作成
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
                'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel'),
                'metadata' => [
                    'item_id' => $item_id,
                ],
            ]);

            // セッションIDを返す
            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            // エラーメッセージをログに記録
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
