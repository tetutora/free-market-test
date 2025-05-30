@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/products/show.css') }}">
@endsection

@section('content')
<div class="product-container">
    <div class="product-image">
        @if(str_starts_with($product->image, 'http'))
            <img src="{{ $product->image }}" alt="{{ $product->name }}" style="max-width: 300px;">
        @else
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 300px;">
        @endif
    </div>
    <div class="product-details">
        <h1>{{ $product->name }}</h1>
        <p class="product-brand">{{ $product->brand_name ?? 'ブランド情報なし' }}</p>
        <p>¥<span class="product-price">{{ number_format(round($product->price)) }}</span>(税込)</p>

        <div class="button-row">
            <div class="favorite-section">
                <button id="favorite-button"
                        class="favorite-button {{ $isFavorited ? 'favorited' : '' }}"
                        data-product-id="{{ $product->id }}">
                    <span id="favorite-icon">{{ $isFavorited ? '★' : '☆' }}</span>
                </button>
                <p id="favorite-count" class="favorite-count">{{ $favoriteCount }}</p>
            </div>
            <div class="icon-button">
                <span class="comment-icon">💬</span>
                <p class="comment-count">{{ $product->comments->count() }}</p>
            </div>
        </div>

        <div class="button-column">
            @if($product->is_sold)
                <div class="purchase-button label-button">SOLD OUT</div>
            @elseif($product->user_id === Auth::id())
                <div class="purchase-button label-button">あなたの出品商品です</div>
            @else
                <form action="{{ route('purchase.show', $product->id) }}" method="GET">
                    <button type="submit" class="purchase-button">購入手続き</button>
                </form>
            @endif
        </div>

        <h2>商品説明</h2>
        <p>{{ $product->description }}</p>

        <h2>商品情報</h2>

        <p class="product-category"><strong>カテゴリ</strong></p>
        <div class="tags">
            @foreach($product->categories as $category)
                <span class="tag">{{ $category->name }}</span>
            @endforeach
        </div>

        <p class="product-status"><strong>商品の状態:</strong></p>
        <div>{{ $product->status ?? '情報なし' }}</div>

        @if($product->comments->isNotEmpty())
        <h2>コメント ({{ $commentCount }})</h2>
        @foreach($product->comments as $comment)
            <div class="comment-item">
                @if($comment->user && $comment->user->profile)
                    <div class="comment-profile">
                        <img src="{{ $comment->user->profile->profile_picture ? asset('storage/' . $comment->user->profile->profile_picture) : asset('images/default-profile.jpg') }}" alt="{{ $comment->user->name }}のプロフィール画像" class="profile-image">
                    </div>
                    <div class="comment-content">
                        <strong>{{ $comment->user->name }}:</strong>
                        <p>{{ $comment->content }}</p>
                    </div>
                @else
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

        <p><strong>商品へのコメント</strong></p>
        <form action="{{ route('product.addComment', $product->id) }}" method="POST">
            @csrf
            <textarea class="textarea" name="content" rows="4" cols="50" placeholder="コメントを入力してください"></textarea>
            <div class="form__error">
            @error('content')
                {{ $message }}
            @enderror
            </div>
            <button type="submit" class="comment-submit-button">コメントを送信</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('favorite-button').addEventListener('click', function () {
        const productId = this.dataset.productId;

        fetch(`/products/${productId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.favorited !== undefined) {
                document.getElementById('favorite-icon').textContent = data.favorited ? '★' : '☆';
                document.getElementById('favorite-count').textContent = data.favoriteCount;
            } else {
                alert(data.message || 'エラーが発生しました。');
            }
        });
    });

    document.getElementById('comment-form').addEventListener('submit', function(e) {
        e.preventDefault();

        fetch('{{ route('product.addComment', $product->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                content: document.querySelector('textarea[name="content"]').value
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else if (data.success) {
                alert(data.success);
                location.reload();
            }
        })
        .catch(error => {
            alert('エラーが発生しました');
        });
    });

</script>
@endsection
