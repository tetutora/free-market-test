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
        <h1>{{ $product->name }}</h1>

        <!-- 価格 -->
        <p><strong>価格:</strong> {{ round($product->price) }}円</p>

        <!-- ボタン: お気に入り & コメント表示 (横並び) -->
        <div class="button-row">
            <button class="favorite-button">お気に入りに登録</button>
            <button class="comment-view-button">コメントを見る</button>
        </div>

        <!-- 購入手続きボタン -->
        <form action="{{ route('purchase.show', $product->id) }}" method="GET">
            <button type="submit" class="purchase-button">購入手続き</button>
        </form>

        <!-- 商品説明 -->
        <h2>商品説明</h2>
        <p>{{ $product->description }}</p>

        <!-- 商品情報 -->
        <h2>商品の情報</h2>

        <!-- カテゴリをタグ形式で表示 -->
        <p><strong>カテゴリ:</strong></p>
        <div class="tags">
            @foreach($product->categories as $category)
                <span class="tag">{{ $category->name }}</span>
            @endforeach
        </div>

        <p><strong>商品の状態:</strong> {{ $product->status->name ?? '情報なし' }}</p>

        <!-- コメント表示欄 -->
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
            <textarea name="comment" rows="4" cols="50" placeholder="コメントを入力してください"></textarea>
            <br>
            <button type="submit" class="comment-submit-button">コメントを送信</button>
        </form>
    </div>
</div>

@endsection
