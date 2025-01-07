@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/create.css') }}">
@endsection

@section('content')

<h1 class="center-title">商品の出品</h1>
<form action="{{ route('sell') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 商品画像 -->
    <div class="form-group">
        <label for="image"><strong>商品画像</strong></label>

        <!-- 画像プレビュー枠 -->
        <div id="image-preview" class="image-preview">
            <p>ここに画像が表示されます</p>
        </div>

        <!-- 画像選択ボタン -->
        <label for="image" class="custom-file-label">画像を選択</label>
        <input type="file" id="image" name="image" accept="image/*" required class="image-upload">
    </div>

    <!-- 商品カテゴリ -->
    <div class="form-group">
        <label for="category_id"><strong>カテゴリ</strong></label>
        <div id="category-tags">
            @foreach($categories as $category)
                <div class="category-tag" data-id="{{ $category->id }}">
                    {{ $category->name }}
                </div>
            @endforeach
        </div>
        <input type="hidden" id="category_id" name="category_id" value="">
    </div>

    <!-- 商品状態 -->
    <div class="form-group">
        <label for="status_id">商品状態</label>
        <select id="status_id" name="status_id" required>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}">{{ $status->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- 商品名と説明 -->
    <div class="form-group">
        <label for="name">商品名</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="description">商品説明</label>
        <textarea id="description" name="description" required></textarea>
    </div>

    <!-- 販売価格 -->
    <div class="form-group">
        <label for="price">価格</label>
        <input type="number" id="price" name="price" required>
    </div>

    <!-- 出品ボタン -->
    <div class="form-group">
        <button type="submit" class="submit-btn">出品する</button>
    </div>
</form>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('image-preview');

        if (imageInput) {
            imageInput.addEventListener('change', function (event) {
                const file = event.target.files[0]; // 選択した画像ファイルを取得
                preview.innerHTML = ''; // プレビューをリセット

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const img = document.createElement('img'); // 画像要素を作成
                        img.src = e.target.result; // 読み込んだ画像データを設定
                        img.style.maxWidth = '100%'; // 最大幅を指定
                        img.style.maxHeight = '100%'; // 最大高さを指定
                        preview.appendChild(img); // プレビュー枠に画像を追加
                    };

                    reader.readAsDataURL(file); // 画像ファイルをデータURLとして読み込む
                } else {
                    preview.innerHTML = '<p>ここに画像が表示されます</p>'; // 画像が選ばれていない場合のメッセージ
                }
            });
        } else {
            console.error('imageInput が見つかりません');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
    const categoryTags = document.querySelectorAll('.category-tag');
    const categoryIdField = document.getElementById('category_id');

    categoryTags.forEach(tag => {
        tag.addEventListener('click', () => {
            tag.classList.toggle('selected');

            const selectedCategories = Array.from(document.querySelectorAll('.category-tag.selected'))
                .map(selectedTag => selectedTag.getAttribute('data-id'));

            categoryIdField.value = selectedCategories.join(',');
        });
    });
});

</script>
@endsection
