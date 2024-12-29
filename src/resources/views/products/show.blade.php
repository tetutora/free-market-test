@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/products/show.css') }}">
@endsection

@section('content')
<div class="product-container">
    <!-- 左側：商品画像 -->
    <div class="product-image">
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 300px;">
    </div>

    <!-- 右側：商品情報 -->
    <div class="product-details">
        <!-- 商品名 -->
        <h1>{{ $product->name }}</h1>

        <!-- 価格 -->
        <p>¥<strong>{{ round($product->price) }}</strong>(税込)</p>

        <!-- お気に入りボタンとコメントボタン (横並び) -->
        <div class="button-row">
            <!-- お気に入りボタン -->
            <div class="icon-button">
                <span class="favorite-icon">☆</span>
                <p class="favorite-count">{{ $product->favorites->count() }}</p>
            </div>
            <!-- コメントボタン -->
            <div class="icon-button">
                <span class="comment-icon">💬</span>
                <p class="comment-count">{{ $product->comments->count() }}</p>
            </div>
        </div>

        <!-- 購入手続きボタン -->
        <div class="button-column">
            <form action="{{ route('purchase.show', $product->id) }}" method="GET">
                <button type="submit" class="purchase-button">購入手続き</button>
            </form>
        </div>

        <!-- 商品説明 -->
        <h2>商品説明</h2>
        <p>{{ $product->description }}</p>

        <!-- カテゴリをタグ形式で表示 -->
        <h2>カテゴリ</h2>
        <div class="tags">
            @foreach($product->categories as $category)
                <span class="tag">{{ $category->name }}</span>
            @endforeach
        </div>

        <!-- 商品の状態 -->
        <p><strong>商品の状態:</strong> {{ $product->status->name ?? '情報なし' }}</p>

        <!-- コメント一覧 -->
        @if($product->comments->isNotEmpty())
            <h2>コメント一覧</h2>
            @foreach($product->comments as $comment)
                <div class="comment-item">
                    <strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}
                </div>
            @endforeach
        @else
            <p>まだコメントはありません。</p>
        @endif

        <!-- コメント入力欄 -->
        <h2>コメントを追加</h2>
        <form action="{{ route('product.comment', $product->id) }}" method="POST">
            @csrf
            <textarea name="content" rows="4" cols="50" placeholder="コメントを入力してください"></textarea>
            <br>
            <button type="submit" class="comment-submit-button">コメントを送信</button>
        </form>
    </div>
</div>
@endsection
