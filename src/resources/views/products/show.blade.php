@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/products/show.css') }}">
@endsection

@section('content')
<div class="product-container">
    <!-- 左側：商品画像 -->
    <div class="product-image">
        @if(str_starts_with($product->image, 'http'))
            <img src="{{ $product->image }}" alt="{{ $product->name }}" style="max-width: 300px;">
        @else
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 500px;">
        @endif
    </div>

    <!-- 右側：商品情報 -->
    <div class="product-details">
        <!-- 商品名 -->
        <h1>{{ $product->name }}</h1>

        <!-- 価格 -->
        <p>¥<span class="product-price">{{ number_format(round($product->price)) }}</span>(税込)</p>

        <!-- お気に入りボタンとコメントボタン (横並び) -->
        <div class="button-row">
            <!-- お気に入りボタン -->
            <div class="icon-button">
                <form action="{{ route('product.favorite', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="favorite-button">
                        <!-- お気に入りの状態に応じて☆マークのスタイルを変更 -->
                        <span class="favorite-icon">
                            {{ $product->favorites->where('user_id', auth()->id())->count() > 0 ? '★' : '☆' }}
                        </span>
                    </button>
                </form>
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

        <h2>商品情報</h2>

        <!-- カテゴリをタグ形式で表示 -->
        <p class="product-category"><strong>カテゴリ</strong></p>
        <div class="tags">
            @foreach($product->categories as $category)
                <span class="tag">{{ $category->name }}</span>
            @endforeach
        </div>

        <!-- 商品の状態 -->
        <p class="product-status"><strong>商品の状態:</strong></p>
        <div>{{ $product->status ?? '情報なし' }}</div>

        <!-- コメント一覧 -->
        @if($product->comments->isNotEmpty())
            <h2>コメント</h2>
            @foreach($product->comments as $comment)
                <div class="comment-item">
                    @if($comment->user && $comment->user->profile)
                        <!-- プロフィール画像 -->
                        <div class="comment-profile">
                            <img 
                                src="{{ $comment->user->profile->profile_picture ? asset('storage/' . $comment->user->profile->profile_picture) : asset('images/default-profile.png') }}" 
                                alt="{{ $comment->user->name }}のプロフィール画像" 
                                class="profile-image"
                            >
                        </div>
                        <!-- コメント内容 -->
                        <div class="comment-content">
                            <strong>{{ $comment->user->name }}:</strong>
                            <p>{{ $comment->content }}</p>
                        </div>
                    @else
                        <!-- ユーザーやプロフィールが存在しない場合 -->
                        <div class="comment-content">
                            <strong>削除されたユーザー:</strong>
                            <p>{{ $comment->content }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="no-comments">まだコメントはありません。</p>
        @endif

        <!-- コメント入力欄 -->
        <p><strong>商品へのコメント</strong></p>
        <form action="{{ route('product.comment', $product->id) }}" method="POST">
            @csrf
            <textarea name="content" rows="4" cols="50" placeholder="コメントを入力してください"></textarea>
            <br>
            <button type="submit" class="comment-submit-button">コメントを送信</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const favoriteBtns = document.querySelectorAll('.favorite-button');

        favoriteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const form = btn.closest('form');
                const productId = form.getAttribute('action').split('/').pop();

                fetch(`/products/${productId}/toggle-favorite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({}),
                })
                .then(response => response.json())
                .then(data => {
                    const favoriteIcon = btn.querySelector('.favorite-icon');
                    if (data.favorited) {
                        favoriteIcon.textContent = '★';
                    } else {
                        favoriteIcon.textContent = '☆';
                    }
                    // 更新するお気に入りカウント
                    const favoriteCount = btn.nextElementSibling;
                    favoriteCount.textContent = data.favoriteCount;
                })
                .catch(error => console.error('エラー:', error));
            });
        });
    });
</script>
@endsection
