@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

@section('content')
    <div class="mypage">
        <div class="mypage-header">
            <!-- プロフィール写真、ユーザー名、プロフィール編集ボタン -->
            <div class="profile-info">
                <img src="{{ $profile_picture ?? asset('images/default-profile.jpg') }}" alt="プロフィール画像" class="profile-image">
                <div class="profile-details">
                    <h2>{{ $profile->name ?? $user->name }}</h2>
                    <a href="{{ route('profile.edit') }}" class="btn-edit-profile">プロフィール編集</a>
                </div>
            </div>
        </div>

        <!-- 出品・購入ボタン -->
        <div class="mypage-buttons">
            <button class="btn-toggle" id="btn-sell">出品した商品</button>
            <button class="btn-toggle" id="btn-purchase">購入した商品</button>
        </div>

        <!-- 商品リスト -->
        <div id="product-list" class="product-list">
            <!-- 出品した商品 -->
            <div class="product-container" id="sell-products">
                @foreach ($user->sales ?? [] as $product)  <!-- $user が null の場合を考慮 -->
                    <div class="product-item">
                        <!-- 商品画像をクリックできるようにする -->
                        <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="product-image">
                            <p class="product-name">{{ $product->name }}</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- 購入した商品 -->
            <div class="product-container" id="purchase-products" style="display: none;">
                @foreach ($user->purchases ?? [] as $product)  <!-- $user が null の場合を考慮 -->
                    <div class="product-item">
                        <!-- 商品画像をクリックできるようにする -->
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnSell = document.getElementById('btn-sell');
            const btnPurchase = document.getElementById('btn-purchase');
            const sellProducts = document.getElementById('sell-products');
            const purchaseProducts = document.getElementById('purchase-products');

            // 出品した商品ボタンが押されたとき
            btnSell.addEventListener('click', () => {
                sellProducts.style.display = 'block';
                purchaseProducts.style.display = 'none';
                btnSell.classList.add('active');
                btnPurchase.classList.remove('active');
            });

            // 購入した商品ボタンが押されたとき
            btnPurchase.addEventListener('click', () => {
                purchaseProducts.style.display = 'block';
                sellProducts.style.display = 'none';
                btnPurchase.classList.add('active');
                btnSell.classList.remove('active');
            });

            // 初期表示：出品した商品を表示
            sellProducts.style.display = 'block';
            purchaseProducts.style.display = 'none';
            btnSell.classList.add('active');
        });
    </script>
@endsection
