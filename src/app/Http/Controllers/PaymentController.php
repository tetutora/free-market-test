<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    // 決済フォームの表示
    public function showPaymentForm()
    {
        return view('payment');
    }

    // 決済処理
    public function processPayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // PaymentIntentの作成
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'jpy',
            ]);

            Log::debug('PaymentIntent created:', ['clientSecret' => $paymentIntent->client_secret]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating PaymentIntent: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
