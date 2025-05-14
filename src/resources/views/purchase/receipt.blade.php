@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/receipt.css') }}">
@endsection

@section('content')
<div class="receipt-container">
    <h1 class="receipt-title">ご購入ありがとうございます</h1>
    <p class="receipt-description">以下の情報をコンビニで提示し、支払いを行ってください。</p>

    <ul class="receipt-info-list">
        <li class="receipt-info-item">商品名：<span class="receipt-product-name">{{ $product->name }}</span></li>
        <li class="receipt-info-item">金額：<span class="receipt-product-price">¥{{ number_format($product->price) }}</span></li>
        <li class="receipt-info-item">注文番号：<span class="receipt-order-id">#{{ $product->id }}{{ now()->timestamp }}</span></li>
    </ul>

    <button class="receipt-print-button" onclick="window.print()">レシートを印刷</button>

    <a href="{{ route('products.index') }}" class="receipt-back-button">商品一覧に戻る</a>
</div>
@endsection
