@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

@section('content')

@php
    $currentPage = request()->query('page', 'sell');
@endphp

<div class="mypage">
    <!-- プロフィール情報 -->
    <div class="mypage-header">
        <div class="profile-info">
            <img src="{{ $profile_picture ?? asset('images/default-profile.jpg') }}" alt="プロフィール画像" class="profile-image">
            <h2>{{ $profile->name ?? $user->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="btn-edit-profile">プロフィール編集</a>
        </div>
    </div>

    <!-- 出品・購入切り替えボタン -->
    <div class="mypage-buttons">
        <button class="btn-toggle" id="btn-sell">出品した商品</button>
        <button class="btn-toggle" id="btn-purchase">購入した商品</button>
    </div>

    <!-- 出品リスト -->
    <div id="product-list" class="product-list">
        <!-- 出品商品リスト -->
        <div class="product-container" id="sell-products" style="display: {{ $currentPage === 'sell' ? 'grid' : 'none' }};">
            @foreach ($user->sales ?? [] as $product)
            <div class="product-item">
                <a href="{{ route('products.show', $product->id) }}">
                    @if(str_starts_with($product->image, 'http'))
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="max-width:600px"> <!-- 外部リンク画像 -->
                    @else
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width:600px"> <!-- ローカル画像 -->
                    @endif
                    <p class="product-name">{{ $product->name }}</p>
                </a>
            </div>
            @endforeach
        </div>

        <!-- 購入リスト -->
        <div class="product-container" id="purchase-products" style="display: {{ $currentPage === 'buy' ? 'grid' : 'none' }};">
            @forelse ($purchasedProducts as $purchase)
                @if ($purchase->product) <!-- null チェックを追加 -->
                    <div class="product-item">
                        <a href="{{ route('products.show', $purchase->product->id) }}" class="product-link">
                            <div class="image-container">
                                @if(str_starts_with($purchase->product->image, 'http'))
                                    <img src="{{ $purchase->product->image }}" alt="{{ $purchase->product->name }}" class="product-img">
                                @else
                                    <img src="{{ asset('storage/' . $purchase->product->image) }}" alt="{{ $purchase->product->name }}" class="product-img">
                                @endif

                                @if($purchase->product->is_sold)
                                    <span class="sold-label">Sold Out</span>
                                @endif
                            </div>
                            <p class="product-name">{{ $purchase->product->name }}</p>
                        </a>
                    </div>
                @endif
            @empty
                <p>購入した商品はありません。</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnSell = document.getElementById('btn-sell');
        const btnPurchase = document.getElementById('btn-purchase');

        if (btnSell && btnPurchase) {
            btnSell.addEventListener('click', () => {
                window.location.href = '/mypage?page=sell'; // 出品ページへ遷移
            });

            btnPurchase.addEventListener('click', () => {
                window.location.href = '/mypage?page=buy'; // 購入ページへ遷移
            });
        } else {
            console.error('ボタンが見つかりません');
        }
    });
</script>
@endsection
