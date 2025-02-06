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
        $paymentMethod = $request->input('payment_method');
        $item_id = $request->input('item_id');

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $product = Product::find($item_id);

        if (!$product) {
            Log::error('商品が見つかりませんでした: item_id = ' . $item_id);
            return response()->json(['error' => '商品が見つかりません'], 404);
        }

        $productName = $product->name;
        $productPrice = $product->price * 1;

        if ($productPrice <= 0) {
            Log::error('無効な商品価格: ' . $productPrice);
            return response()->json(['error' => '商品価格が無効です'], 400);
        }

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
                'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel'),
                'metadata' => [
                    'item_id' => $item_id,
                ],
            ]);

            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
