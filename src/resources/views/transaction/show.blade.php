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

    <div class="main-content">
        <div class="transaction-header">
            <div class="profile-info">
                <img src="{{ $transaction->product->user->profile_picture ?? asset('images/default-profile.jpg') }}" alt="プロフィール画像" class="profile-image">
                <h2>{{ $transaction->product->user->name }}さんとの取引画面</h2>
            </div>
            @if (auth()->id() === $transaction->user_id)
                <div class="complete-button">
                    <button class="btn-complete">取引を完了する</button>
                </div>
            @endif
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
                            @if ($message->image_path)
                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="画像" class="message-image">
                            @endif
                            <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                            @if ($message->sender_id === auth()->id())
                                @if($message->is_read)
                                    <span class="read-label">既読</span>
                                @else
                                    <span class="unread-label">未読</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('transaction.sendMessage', $transaction->id) }}" method="POST" enctype="multipart/form-data" class="chat-form">
                @csrf
                <textarea name="body" id="unsent-message" placeholder="メッセージを入力..." rows="3">{{ old('body') }}</textarea>
                @if ($errors->has('body'))
                    <div class="form__error">
                        <p>{{ $errors->first('body') }}</p>
                    </div>
                @endif
                <input type="file" name="image" id="imageInput" accept=".jpeg,.jpg,.png" style="display:none;" onchange="previewImage();">
                @if ($errors->has('image'))
                    <div class="form__error">
                        <p>{{ $errors->first('image') }}</p>
                    </div>
                @endif
                <div class="chat-form-actions">
                    <button type="button" class="btn-image-add" onclick="document.getElementById('imageInput').click()">画像追加</button>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>

            <div id="imagePreviewContainer" style="display: none; margin-top: 10px;">
                <h5>画像プレビュー:</h5>
                <img id="imagePreview" src="" alt="選択した画像のプレビュー" style="max-width: 100%; height: auto;"/>
            </div>
        </div>
    </div>
</div>
<!-- 評価ポップアップ -->
<div id="ratingPopup" class="rating-popup">
    <div class="rating-popup-content">
        <h3>評価を送信してください</h3>
        <div class="rating-stars">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <div class="rating-actions">
            <button class="btn-cancel" onclick="closeRatingPopup()">キャンセル</button>
            <button class="btn-submit" onclick="submitRating()">評価を送信</button>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    const messageKey = 'unsent_message_{{ $transaction->id }}';

    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('unsent-message');
        const chatMessages = document.querySelector('.chat-messages');

        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        const savedMessage = localStorage.getItem(messageKey);
        if (savedMessage) {
            textarea.value = savedMessage;
        }

        textarea.addEventListener('input', () => {
            localStorage.setItem(messageKey, textarea.value);
        });

        const form = textarea.closest('form');
        form.addEventListener('submit', () => {
            localStorage.removeItem(messageKey);
        });
    });

    function previewImage() {
        const fileInput = document.getElementById('imageInput');
        const previewImage = document.getElementById('imagePreview');
        const previewContainer = document.getElementById('imagePreviewContainer');

        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    }

    let selectedRating = null;

    document.querySelectorAll('.rating-stars .star').forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = star.dataset.value;

            // 星の選択状態を変更
            document.querySelectorAll('.rating-stars .star').forEach(s => {
                s.classList.remove('selected');
            });

            // 選択した星以下の星に selected クラスを追加
            document.querySelectorAll('.rating-stars .star').forEach(s => {
                if (s.dataset.value <= selectedRating) {
                    s.classList.add('selected');
                }
            });
        });
    });

    function closeRatingPopup() {
        document.getElementById('ratingPopup').style.display = 'none';
    }

    function submitRating() {
        if (!selectedRating) {
            alert('評価を選択してください');
            return;
        }

        const transactionId = {{ $transaction->id }};

        fetch(`/transactions/${transactionId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ rating: selectedRating })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('評価が送信され、取引が完了しました');
                closeRatingPopup();
                window.location.href = '{{ route('home') }}';
            } else {
                alert(`評価の送信に失敗しました: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('評価の送信中にエラーが発生しました');
        });
    }
</script>
@if ($showRatingModalForSeller)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('ratingPopup').style.display = 'flex';
        });
    </script>
@endif
@endsection
