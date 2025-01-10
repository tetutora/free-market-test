@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

@section('content')
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

    <!-- 商品リスト -->
    <div id="product-list" class="product-list">
        <!-- 出品商品リスト -->
        <div class="product-container" id="sell-products">
            @foreach ($user->sales ?? [] as $product)
            <div class="product-item">
                <a href="{{ route('product.show', $product->id) }}">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="product-image">
                    <p class="product-name">{{ $product->name }}</p>
                </a>
            </div>
            @endforeach
        </div>

        <!-- 購入商品リスト -->
        <div class="product-container" id="purchase-products" style="display: none;">
            @foreach ($user->purchases ?? [] as $product)
            <div class="product-item">
                <a href="{{ route('product.show', $product->id) }}">
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="商品画像" class="product-image">
                    <p class="product-name">{{ $product->name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnSell = document.getElementById('btn-sell');
        const btnPurchase = document.getElementById('btn-purchase');
        const sellProducts = document.getElementById('sell-products');
        const purchaseProducts = document.getElementById('purchase-products');

        // 出品ボタンのクリック処理
        btnSell.addEventListener('click', () => {
            sellProducts.style.display = 'block';
            purchaseProducts.style.display = 'none';
            btnSell.classList.add('active');
            btnPurchase.classList.remove('active');
        });

        // 購入ボタンのクリック処理
        btnPurchase.addEventListener('click', () => {
            purchaseProducts.style.display = 'block';
            sellProducts.style.display = 'none';
            btnPurchase.classList.add('active');
            btnSell.classList.remove('active');
        });

        // 初期表示：出品商品を表示
        btnSell.click();
    });
</script>
@endsection
