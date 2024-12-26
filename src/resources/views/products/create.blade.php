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
        <label for="image">商品画像</label>
        <input type="file" id="image" name="image" accept="image/*" required class="image-upload">
    </div>

    <!-- 商品カテゴリ -->
    <div class="form-group">
        <label for="category_id">カテゴリ</label>
        <div id="category-tags">
            @foreach($categories as $category)
                <div class="category-tag">
                    <input type="checkbox" name="category_id[]" value="{{ $category->id }}" id="category_{{ $category->id }}">
                    <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
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
    // チェックボックスが選択されたときの処理
    document.querySelectorAll('input[name="category_id[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // 選択されたカテゴリのIDを配列に追加/削除
            let selectedCategories = [];
            document.querySelectorAll('input[name="category_id[]"]:checked').forEach(function(checkedCheckbox) {
                selectedCategories.push(checkedCheckbox.value);
            });

            // 隠しフィールドに選択されたカテゴリのIDをカンマ区切りで設定
            document.getElementById('category_id').value = selectedCategories.join(',');
        });
    });
</script>
@endsection
