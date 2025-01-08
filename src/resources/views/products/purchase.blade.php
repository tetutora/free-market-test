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
                    <option value="credit_card">カード払い</option>
                    <option value="bank_transfer">コンビニ払い</option>
                </select>
            </div>

            <hr class="section-divider">

            <div class="delivery-address">
                <h3>配送先 <a href="{{ route('profile.address.edit', ['productId' => $product->id]) }}" class="address-change-button">住所変更</a></h3>
                <p><strong>〒 {{$zipcode }}</strong></p>
                <p><strong>{{ $address }} {{ $building }}</strong> </p>
            </div>
        </div>

        <div class="purchase-right">
            <p><strong>商品代金</strong> ¥{{ number_format(round($product->price)) }}</p>
            <p><strong>支払い方法:</strong> <span id="selected-payment-method">カード払い</span></p>
            <form action="{{ route('purchase.complete', ['productId' => $product->id]) }}" method="POST">
                @csrf
                <button type="submit" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    function updatePaymentMethod() {
        var paymentMethod = document.getElementById('payment-method').value;
        var methodText = '';

        if (paymentMethod === 'credit_card') {
            methodText = 'カード払い';
        } else if (paymentMethod === 'bank_transfer') {
            methodText = 'コンビニ払い';
        }

        document.getElementById('selected-payment-method').textContent = methodText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePaymentMethod();
    });
</script>
@endsection


@endsection
