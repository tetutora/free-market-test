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
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-image">
                    @else
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                    @endif
                </div>
                <div class="purchase-details">
                    <h3 class="purchase-title">{{ $product->name }}</h3>
                    <p><strong>¥</strong> {{ number_format(round($product->price)) }}</p>
                </div>
            </div>
            <hr class="section-divider">
            <div class="payment-method">
                <label for="payment-method" class="payment-label"><strong>お支払い方法</strong></label>
                <select name="payment-method" id="payment-method" class="payment-select" onchange="updatePaymentMethod()">
                    <option value="credit_card">カード払い</option>
                    <option value="bank_transfer">コンビニ払い</option>
                </select>
            </div>
            <hr class="section-divider">
            <div class="delivery-address">
                <h3 class="address-title">配送先 <a href="{{ route('profile.address.edit', ['item_id' => $product->id]) }}" class="address-change-button">住所変更</a></h3>
                <p><strong>〒 {{$zipcode }}</strong></p>
                <p><strong>{{ $address }} {{ $building }}</strong></p>
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
        const paymentMethod = document.getElementById('payment-method').value;
        const itemId = "{{ $product->id }}";

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
            if (session.redirect_url) {
                // コンビニ払い → レシート印刷画面へ
                window.location.href = session.redirect_url;
            } else if (session.id) {
                // カード払い → Stripe Checkout
                return stripe.redirectToCheckout({ sessionId: session.id });
            } else {
                alert("決済処理に失敗しました。");
            }
        })
        .catch(error => console.error("Error:", error));
    });
</script>
@endsection
