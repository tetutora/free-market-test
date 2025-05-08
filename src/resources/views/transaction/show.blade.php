@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction/show.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endsection

@section('content')
<div class="transaction-wrapper">
    <!-- 左側：その他の取引中商品 -->
    <div class="sidebar">
        <h4>他の取引中の商品</h4>
        <ul class="other-products">
            @foreach ($otherTransactions as $other)
                <li class="{{ $other->id === $transaction->id ? 'active' : '' }}">
                    <a href="{{ route('transaction.show', $other->id) }}">
                        {{ $other->product->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- 右側：現在の取引詳細 -->
    <div class="main-content">
        <div class="transaction-header">
            <div class="profile-info">
                <img src="{{ $transaction->product->user->profile_picture ?? asset('images/default-profile.jpg') }}" alt="プロフィール画像" class="profile-image">
                <h2>{{ $transaction->product->user->name }}さんとの取引画面</h2>
            </div>
            <div class="complete-button">
                <button class="btn-complete">取引を完了する</button>
            </div>
        </div>

        <div class="product-info">
            <div class="product-image">
                @if(str_starts_with($transaction->product->image, 'http'))
                    <img src="{{ $transaction->product->image }}" alt="{{ $transaction->product->name }}">
                @else
                    <img src="{{ asset('storage/' . $transaction->product->image) }}" alt="{{ $transaction->product->name }}">
                @endif
            </div>
            <div class="product-details">
                <h3 class="product-name">{{ $transaction->product->name }}</h3>
                <p class="product-price">価格: ¥{{ number_format($transaction->product->price) }}</p>
            </div>
        </div>

        <div class="chat-container">
            <div class="chat-header">
                <h4>取引チャット</h4>
            </div>
            <div class="chat-messages">
                @foreach ($transaction->messages as $message)
                    <div class="chat-message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        <div class="message-header">
                            <img src="{{ $message->sender->profile_picture ?? asset('images/default-profile.jpg') }}" alt="アイコン" class="message-avatar">
                            <span class="message-username">{{ $message->sender->name }}</span>
                        </div>
                        <div class="message-body">
                            <p>{{ $message->body }}</p>
                            <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('transaction.sendMessage', $transaction->id) }}" method="POST" class="chat-form">
                @csrf
                <textarea name="message" placeholder="メッセージを入力..." rows="3" required></textarea>
                <div class="chat-form-actions">
                    <button type="button" class="btn-image-add">画像追加</button>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatMessages = document.querySelector('.chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>
@endsection
