@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/index.css') }}">
@endsection

@section('content')

@php
    $currentPage = request()->query('page', 'recommendation'); // デフォルトは "recommendation"
@endphp

<!-- ボタンを配置 -->
<div class="header-buttons">
    <button id="recommendation-btn" class="btn">おすすめ</button>
    <button id="mylist-btn" class="btn">マイリスト</button>
</div>

<!-- 商品一覧 (おすすめ表示) -->
<div id="product-list" class="product-list" style="display: {{ $currentPage === 'recommendation' ? 'flex' : 'none' }};">
    @foreach($products as $product)
        <div class="product-item">
            <a href="{{ route('products.show', $product->id) }}">
                @if(str_starts_with($product->image, 'http'))
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"> <!-- 外部リンク画像 -->
                @else
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"> <!-- ローカル画像 -->
                @endif
            </a>
            <p>{{ $product->name }}
                @if($product->is_sold)
                    <span class="sold-label">Sold</span>
                @endif
            </p>
        </div>
    @endforeach
</div>

<!-- マイリスト表示 -->
<div id="mylist" class="product-list" style="display: {{ $currentPage === 'mylist' ? 'flex' : 'none' }};">
    @if($likedProducts->isNotEmpty())
        @foreach($likedProducts as $likedProduct)
            <div class="product-item">
                <a href="{{ route('products.show', $likedProduct->id) }}">
                    @if(str_starts_with($likedProduct->image, 'http')) <!-- 外部リンク対応 -->
                        <img src="{{ $likedProduct->image }}" alt="{{ $likedProduct->name }}">
                    @else
                        <img src="{{ asset('storage/' . $likedProduct->image) }}" alt="{{ $likedProduct->name }}">
                    @endif
                </a>
                <p>{{ $likedProduct->name }}
                    @if($likedProduct->is_sold)
                        <span class="sold-label">Sold</span>
                    @endif
                </p>
            </div>
        @endforeach
    @else
        <p>マイリストに商品はありません。</p>
    @endif
</div>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const recommendationBtn = document.getElementById('recommendation-btn');
        const mylistBtn = document.getElementById('mylist-btn');

        if (recommendationBtn && mylistBtn) {
            recommendationBtn.addEventListener('click', () => {
                window.location.href = '/?page=recommendation'; // 「おすすめ」ページへ遷移
            });

            mylistBtn.addEventListener('click', () => {
                window.location.href = '/?page=mylist'; // 「マイリスト」ページへ遷移
            });
        } else {
            console.error('ボタンが見つかりません');
        }
    });
</script>
@endsection
