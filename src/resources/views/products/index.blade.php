@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/index.css') }}">
@endsection

@section('content')

@php
    $currentPage = request()->query('page', 'recommendation');
@endphp

<div class="header-buttons">
    <button id="recommendation-btn" class="btn">おすすめ</button>
    <button id="mylist-btn" class="btn">マイリスト</button>
</div>

<div id="product-list" class="product-list" style="display: {{ $currentPage === 'recommendation' ? 'flex' : 'none' }};">
    @foreach($products as $product)
        <div class="product-item">
            <a href="{{ route('products.show', $product->id) }}">
                @if(str_starts_with($product->image, 'http'))
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                @else
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @endif
            </a>
            <p>{{ $product->name }}
                @if($product->is_sold)
                    <span class="sold-label">Sold Out</span>
                @endif
            </p>
        </div>
    @endforeach
</div>

<div id="mylist" class="product-list" style="display: {{ $currentPage === 'mylist' ? 'flex' : 'none' }};">
    @if($likedProducts && $likedProducts->isNotEmpty())
        @foreach($likedProducts as $likedProduct)
            <div class="product-item">
                <a href="{{ route('products.show', $likedProduct->id) }}">
                    @if(str_starts_with($likedProduct->image, 'http'))
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
                window.location.href = '/?page=recommendation&search={{ request('search') }}';
            });
            mylistBtn.addEventListener('click', () => {
                window.location.href = '/?page=mylist&search={{ request('search') }}';
            });
        } else {
            console.error('ボタンが見つかりません');
        }
    });
</script>
@endsection