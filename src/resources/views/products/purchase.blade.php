@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <!-- 左側：商品画像、商品名、価格、支払い方法、配送先 -->
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
                    <option value="credit_card">選択してください</option>
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

            <!-- Stripeのカード入力フォーム -->
            <div id="stripe-payment" style="display:none;">
                <label for="card-element">クレジットカード情報</label>
                <div id="card-element">
                    <!-- Stripe Card Element will go here -->
                </div>
            </div>
        </div>

        <div class="purchase-right">
            <p><strong>商品代金</strong> ¥{{ number_format(round($product->price)) }}</p>
            <p><strong>支払い方法:</strong> <span id="selected-payment-method">カード払い</span></p>
            <form action="{{ route('purchase.complete', ['item_id' => $product->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $product->id }}">
                <button type="submit" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</div>

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Stripeの公開鍵を設定
    var stripe = Stripe('your-publishable-key'); // ここに自分の公開鍵を挿入
    var elements = stripe.elements();

    // カード情報の入力エリアを作成
    var cardElement = elements.create('card'); // 'card'を指定してカード情報を入力させる

    // カードエレメントを#card-elementにマウント
    cardElement.mount('#card-element');

    // 支払い情報を処理するフォーム送信時の処理
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        stripe.createToken(cardElement).then(function(result) {
            if (result.error) {
                // エラーがあれば、エラーメッセージを表示
                alert(result.error.message);
            } else {
                // トークンを作成した後、そのトークンをフォームに追加して送信
                var tokenInput = document.createElement('input');
                tokenInput.setAttribute('type', 'hidden');
                tokenInput.setAttribute('name', 'stripe_token');
                tokenInput.setAttribute('value', result.token.id);
                document.querySelector('form').appendChild(tokenInput);

                // フォームを送信
                document.querySelector('form').submit();
            }
        });
    });

    // 支払い方法の選択肢に応じてStripeのカード入力フォームを表示・非表示
    function updatePaymentMethod() {
        var paymentMethod = document.getElementById('payment-method').value;

        if (paymentMethod === 'credit_card') {
            document.getElementById('stripe-payment').style.display = 'block'; // カード情報入力フォームを表示
        } else {
            document.getElementById('stripe-payment').style.display = 'none'; // 非表示
        }

        var methodText = paymentMethod === 'credit_card' ? 'カード払い' : 'コンビニ払い';
        document.getElementById('selected-payment-method').textContent = methodText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePaymentMethod();
    });
</script>
@endsection

@endsection
