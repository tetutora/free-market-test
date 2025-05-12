@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <div class="purchase-left">
            <div class="purchase-info">
                <div class="purchase-image">
                    @if(str_starts_with($product->image, 'http'))
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 100%; height: auto; border-radius: 5px;">
                    @else
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: auto; border-radius: 5px;">
                    @endif
                </div>
                <div class="purchase-details">
                    <h3>{{ $product->name }}</h3>
                    <p><strong>¥</strong> {{ number_format(round($product->price)) }}</p>
                </div>
            </div>

            <hr class="section-divider">

            <div class="payment-method">
                <label for="payment-method"><strong>お支払い方法</strong></label>
                <select name="payment-method" id="payment-method" onchange="updatePaymentMethod()">
                    <option value="credit_card">カード払い</option>
                    <option value="bank_transfer">コンビニ払い</option>
                </select>
            </div>

            <hr class="section-divider">

            <div class="delivery-address">
                <h3>配送先 <a href="{{ route('profile.address.edit', ['item_id' => $product->id]) }}" class="address-change-button">住所変更</a></h3>
                <p><strong>〒 {{$zipcode }}</strong></p>
                <p><strong>{{ $address }} {{ $building }}</strong> </p>
            </div>
        </div>

        <div class="purchase-right">
            <p><strong>商品代金</strong> ¥{{ number_format(round($product->price)) }}</p>
            <p><strong>支払い方法:</strong> <span id="selected-payment-method">カード払い</span></p>
            <form id="payment-form">
                <button type="submit" id="submit-payment" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    var stripe = Stripe("{{ config('services.stripe.key') }}");
    var checkoutButton = document.getElementById('submit-payment');

    function updatePaymentMethod() {
        var paymentMethod = document.getElementById('payment-method').value;
        var methodText = paymentMethod === 'credit_card' ? 'カード払い' : 'コンビニ払い';
        document.getElementById('selected-payment-method').textContent = methodText;
    }

    checkoutButton.addEventListener('click', function(e) {
    e.preventDefault();
        var paymentMethod = document.getElementById('payment-method').value;

        var itemId = "{{ $product->id }}";

        fetch('/create-checkout-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                item_id: itemId
            })
        })
        .then(response => response.json())
        .then(session => {
            console.log("Checkout Session:", session);
            if (session.id) {
                return stripe.redirectToCheckout({ sessionId: session.id });
            } else {
                alert("決済セッションの作成に失敗しました。");
            }
        })
        .catch(error => console.error("Error:", error));
    });
</script>
@endsection
