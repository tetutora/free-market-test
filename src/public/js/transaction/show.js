document.addEventListener('DOMContentLoaded', () => {
    const messageKey = 'unsent_message_{{ $transaction->id }}';

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
    if (form) {
        form.addEventListener('submit', () => {
            localStorage.removeItem(messageKey);
        });
    }

    const completeButton = document.querySelector('.btn-complete');
    if (completeButton) {
        completeButton.addEventListener('click', () => {
            document.getElementById('ratingPopup').style.display = 'flex';
        });
    }

    document.querySelectorAll('.rating-stars .star').forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = star.dataset.value;
            document.querySelectorAll('.rating-stars .star').forEach(s => {
                s.classList.toggle('selected', s.dataset.value <= selectedRating);
            });
        });
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

function closeRatingPopup() {
    document.getElementById('ratingPopup').style.display = 'none';
}

function submitRating() {
    if (!selectedRating) {
        alert("評価を選択してください。");
        return;
    }

    fetch("{{ route('transaction.submitRating', $transaction->id) }}", {
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
            window.location.href = "{{ route('home') }}";
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('評価の送信中にエラーが発生しました');
    });
}

function editMessage(messageId) {
    const messageBody = document.querySelector(`#message-${messageId} .message-content`);
    const messageText = messageBody.textContent || messageBody.innerText;

    const editFormHTML = `
        <textarea id="editMessageText" rows="3">${messageText}</textarea>
        <button onclick="saveEditedMessage(${messageId})">保存</button>
        <button onclick="cancelEditMessage()">キャンセル</button>
    `;
    messageBody.innerHTML = editFormHTML;
}

function saveEditedMessage(messageId) {
    const editedText = document.getElementById('editMessageText').value;

    fetch(`/transactions/messages/${messageId}/edit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ body: editedText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('メッセージが更新されました');
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('メッセージの更新に失敗しました');
    });
}

function cancelEditMessage() {
    location.reload();
}

function deleteMessage(messageId) {
    if (confirm('このメッセージを削除しますか？')) {
        fetch(`/transactions/messages/${messageId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('メッセージが削除されました');
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('メッセージの削除に失敗しました');
        });
    }
}