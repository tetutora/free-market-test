<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $paymentMethod = $request->input('payment_method');
        $item_id = $request->input('item_id');
        $user = Auth::user();

        $product = Product::find($item_id);
        if (!$product) {
            Log::error('商品が見つかりませんでした: item_id = ' . $item_id);
            return response()->json(['error' => '商品が見つかりません'], 404);
        }

        if ($paymentMethod === 'bank_transfer') {
            if ($product->is_sold) {
                return response()->json(['error' => 'この商品はすでに購入されています'], 400);
            }

            $purchase = new Purchase();
            $purchase->product_id = $product->id;
            $purchase->user_id = $user->id;
            $purchase->seller_id = $product->user_id;
            $purchase->status = 'trading';
            $purchase->save();

            $product->is_sold = true;
            $product->save();

            return response()->json([
                'redirect_url' => route('receipt.print', ['item_id' => $item_id])
            ]);
        }

        $stripeApiKey = env('STRIPE_SECRET');
        Stripe::setApiKey($stripeApiKey);

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
                        'product_data' => ['name' => $productName],
                        'unit_amount' => $productPrice,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel'),
                'metadata' => ['item_id' => $item_id],
            ]);

            $purchase = new Purchase();
            $purchase->product_id = $product->id;
            $purchase->user_id = $user->id;
            $purchase->seller_id = $product->user_id;
            $purchase->status = 'trading';
            $purchase->save();

            $product->is_sold = true;
            $product->save();

            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}