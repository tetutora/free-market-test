@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-content">
        <div class="purchase-left">
            <!-- 商品画像、商品名、価格、支払い方法、配送先情報を表示 -->
            <div class="purchase-image">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 300px;">
            </div>
            <h1>{{ $product->name }}</h1>
            <p><strong>価格:</strong> {{ round($product->price) }}円</p>
            <div class="payment-method">
                <label for="payment-method">お支払い方法</label>
                <select name="payment-method" id="payment-method">
                    <option value="credit_card">クレジットカード</option>
                    <option value="bank_transfer">銀行振込</option>
                    <option value="cash_on_delivery">代金引換</option>
                </select>
            </div>

            <!-- 住所変更 -->
            <div class="delivery-address">
                <h3>配送先</h3>
                @if(Auth::user()->address)
                    <p><strong>郵便番号:</strong> {{ Auth::user()->address->postal_code }}</p>
                    <p><strong>住所:</strong> {{ Auth::user()->address->address }}</p>
                    <a href="{{ route('profile.address.edit') }}" class="address-change-button">住所変更</a>
                @else
                    <p>住所が登録されていません。</p>
                    <a href="{{ route('profile.address.edit') }}" class="address-change-button">住所を登録する</a>
                @endif
            </div>
        </div>

        <!-- 右側：確認と購入ボタン -->
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
