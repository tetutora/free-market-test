@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <!-- 左側：商品画像、商品名、価格、支払い方法、配送先 -->
        <div class="purchase-left">
            <!-- 商品画像 -->
            <div class="purchase-image">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100%; height: auto; border-radius: 5px;">
            </div>
            <!-- 商品名 -->
            <h1>{{ $product->name }}</h1>
            <hr class="section-divider"> <!-- アンダーライン -->
            <!-- 商品価格 -->
            <p><strong>価格:</strong> {{ round($product->price) }}円</p>
            <hr class="section-divider"> <!-- アンダーライン -->

            <!-- 支払い方法 -->
            <div class="payment-method">
                <label for="payment-method">お支払い方法</label>
                <select name="payment-method" id="payment-method">
                    <option value="credit_card">クレジットカード</option>
                    <option value="bank_transfer">銀行振込</option>
                    <option value="cash_on_delivery">代金引換</option>
                </select>
            </div>
            <hr class="section-divider"> <!-- アンダーライン -->

            <!-- 配送先 -->
            <!-- 配送先 -->
            <div class="delivery-address">
                <h3>配送先 <a href="{{ route('profile.address.edit') }}" class="address-change-button">住所変更</a></h3>
                <p><strong>郵便番号:</strong> {{$zipcode }}</p>
                <p><strong>住所:</strong> {{ $address }}</p>
                <p><strong>建物名:</strong> {{ $building }}</p>
            </div>
        </div>

        <!-- 右側：購入確認 -->
        <div class="purchase-right">
            <h2>購入確認</h2>
            <p><strong>商品代金:</strong> {{ round($product->price) }}円</p>
            <p><strong>選択したお支払い方法:</strong> クレジットカード</p>
            <form action="{{ route('purchase.complete', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</div>
@endsection
