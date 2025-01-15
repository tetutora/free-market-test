@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/index.css') }}">
@endsection

@section('content')

<!-- ボタンを配置 -->
<div class="header-buttons">
    <button id="recommendation-btn" class="btn">おすすめ</button>
    <button id="mylist-btn" class="btn">マイリスト</button>
</div>

<!-- 商品一覧 (おすすめ表示) -->
<div id="product-list" class="product-list">
    @foreach($products as $product)
        <div class="product-item">
            <a href="{{ route('product.show', $product->id) }}">
                @if(str_starts_with($product->image, 'http')) <!-- 外部リンクかどうかチェック -->
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"> <!-- 外部リンク画像 -->
                @else
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"> <!-- ローカル画像 -->
                @endif
            </a>
            <p>
                {{ $product->name }}
                @if($product->is_sold)
                    <span class="sold-label">Sold</span>
                @endif
            </p>
        </div>
    @endforeach
</div>

<!-- マイリスト表示 (初期は非表示) -->
<div id="mylist" class="product-list" style="display: none;">
    <h1>マイリスト</h1>
    @if(Auth::check() && Auth::user()->is_approved)
        @foreach($likedProducts as $likedProduct)
            <div class="product-item">
                <a href="{{ route('product.show', $likedProduct->id) }}">
                    <img src="{{ asset('storage/' . $likedProduct->image) }}" alt="{{ $likedProduct->name }}">
                </a>
                <p>
                    {{ $likedProduct->name }}
                    @if($likedProduct->is_sold)
                        <span class="sold-label">Sold</span>
                    @endif
                </p>
            </div>
        @endforeach
    @else
        <p>マイリストは表示されません。</p>
    @endif
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const recommendationBtn = document.getElementById('recommendation-btn');
        const mylistBtn = document.getElementById('mylist-btn');
        const productList = document.getElementById('product-list');
        const mylist = document.getElementById('mylist');

        if (recommendationBtn && mylistBtn && productList && mylist) {
            recommendationBtn.addEventListener('click', () => {
                productList.style.display = 'flex';
                mylist.style.display = 'none';
            });

            mylistBtn.addEventListener('click', () => {
                productList.style.display = 'none';
                mylist.style.display = 'flex';
            });
        } else {
            console.error('ボタンまたは表示要素が見つかりません');
        }
    });
</script>
@endsection
