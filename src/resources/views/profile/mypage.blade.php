@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

@section('content')

@php
    $currentPage = request()->query('page', 'sell');
@endphp
<div class="mypage">
    <div class="mypage-header">
        <div class="profile-info">
        <img src="{{ $profile_picture ?? asset('images/default-profile.jpg') }}" alt="プロフィール画像" class="profile-image">
        <div class="name-rating">
        <h2 class="user-name">{{ $profile->name ?? $user->name }}</h2>
        @if(!is_null($allReceivedRatingsRounded))
            <div class="rating">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $allReceivedRatingsRounded)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
        @endif
    </div>
        <a href="{{ route('profile.edit') }}" class="btn-edit-profile">プロフィール編集</a>
    </div>
    </div>

    <div class="mypage-buttons">
        <button class="btn-toggle {{ $currentPage === 'sell' ? 'active' : '' }}" id="btn-sell">出品した商品</button>
        <button class="btn-toggle {{ $currentPage === 'completed' ? 'active' : '' }}" id="btn-purchase">購入した商品</button>
        <button class="btn-toggle {{ $currentPage === 'trading' ? 'active' : '' }}" id="btn-trading">
            取引中の商品
            @if($unreadMessageCount > 0)
                <span class="badge">{{ $unreadMessageCount }}</span>
            @endif
        </button>
    </div>

    <div id="product-list" class="product-list">
        <div class="product-container" id="sell-products" style="display: {{ $currentPage === 'sell' ? 'grid' : 'none' }};">
            @forelse ($user->sales ?? [] as $product)
                <div class="product-item">
                    <a href="{{ route('products.show', $product->id) }}">
                        @if(str_starts_with($product->image, 'http'))
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" style="max-width:600px">
                        @else
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width:600px">
                        @endif
                        <p class="product-name">{{ $product->name }}</p>
                    </a>
                </div>
            @empty
                <p>出品した商品はありません。</p>
            @endforelse
        </div>

        <div class="product-container" id="purchase-products" style="display: {{ $currentPage === 'completed' ? 'grid' : 'none' }};">
            @forelse ($purchasedProducts as $purchase)
                @if ($purchase->product)
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

        <div class="product-container" id="trading-products" style="display: {{ $currentPage === 'trading' ? 'grid' : 'none' }};">
            @forelse ($allTradingProducts as $purchase)
                <div class="product-item">
                    <a href="{{ route('transaction.show', $purchase->id) }}">
                        <div class="image-container">
                            @if(str_starts_with($purchase->product->image, 'http'))
                                <img src="{{ $purchase->product->image }}" alt="{{ $purchase->product->name }}" class="product-img">
                            @else
                                <img src="{{ asset('storage/' . $purchase->product->image) }}" alt="{{ $purchase->product->name }}" class="product-img">
                            @endif
                            @if($purchase->unread_messages_count > 0)
                                <span class="badge message-badge">{{ $purchase->unread_messages_count }}</span>
                            @endif
                        </div>
                        <p class="product-name">{{ $purchase->product->name }}</p>
                    </a>
                </div>
            @empty
                <p>取引中の商品はありません。</p>
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
        const btnTrading = document.getElementById('btn-trading');

        if (btnSell && btnPurchase) {
            btnSell.addEventListener('click', () => {
                window.location.href = '/mypage?page=sell';
            });

            btnPurchase.addEventListener('click', () => {
                window.location.href = '/mypage?page=completed';
            });

            btnTrading.addEventListener('click', () => {
                window.location.href = '/mypage?page=trading';
            });
        } else {
            console.error('ボタンが見つかりません');
        }
    });
</script>
@endsection
