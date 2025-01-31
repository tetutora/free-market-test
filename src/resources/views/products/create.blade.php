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
        <div id="image-preview" class="image-preview">画像を選択してください</div>
        <label for="image" class="custom-file-label">画像を選択</label>
        <input type="file" id="image" name="image" accept="image/*" required class="image-upload">
    </div>
    @error('image')
        {{ $message }}
    @enderror

    <!-- 商品カテゴリ -->
    <div class="form-group">
        <label for="categories"><strong>カテゴリ</strong></label>
        <div id="categories">
            @foreach($categories as $category)
                <div class="category-tag" data-id="{{ $category->id }}">{{ $category->name }}</div>
            @endforeach
        </div>
        <input type="hidden" id="category_id" name="category_id" value="">
    </div>
    @error('category_id')
        {{ $message }}
    @enderror


    <!-- 商品状態 -->
    <div class="form-group">
        <label for="status"><strong>商品状態</strong></label>
        <select id="status" name="status" required>
            <option value="">-- 選択してください --</option>
            <option value="良好">良好</option>
            <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
            <option value="やや傷や汚れあり">やや傷や汚れあり</option>
            <option value="状態が悪い">状態が悪い</option>
        </select>
    </div>
    @error('status')
        {{ $message }}
    @enderror


    <!-- 商品名とブランド名と商品説明 -->
    <div class="form-group">
        <label for="name"><strong>商品名</strong></label>
        <input type="text" id="name" name="name" required>
    </div>
    @error('name')
        {{ $message }}
    @enderror

    <div class="form-group">
        <label for="brand_name"><strong>ブランド名</strong></label>
        <input type="text" id="brand_name" name="brand_name">
    </div>
    @error('brand_name')
        {{ $message }}
    @enderror

    <div class="form-group">
        <label for="description"><strong>商品説明</strong></label>
        <textarea id="description" name="description" required></textarea>
    </div>
    @error('description')
        {{ $message }}
    @enderror

    <!-- 販売価格 -->
    <div class="form-group">
        <label for="price"><strong>価格</strong></label>
        <input type="number" id="price" name="price" required>
    </div>
    @error('price')
        {{ $message }}
    @enderror


    <!-- 出品ボタン -->
    <div class="form-group">
        <button type="submit" class="submit-btn">出品する</button>
    </div>
</form>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryTags = document.querySelectorAll('.category-tag');
        const categoryIdInput = document.getElementById('category_id');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');

        let selectedCategories = [];

        // カテゴリ選択
        categoryTags.forEach(tag => {
            tag.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-id');

                if (selectedCategories.includes(categoryId)) {
                    // すでに選択済みの場合、選択を解除
                    selectedCategories = selectedCategories.filter(id => id !== categoryId);
                    this.classList.remove('selected');
                } else {
                    // 未選択の場合、選択リストに追加
                    selectedCategories.push(categoryId);
                    this.classList.add('selected');
                }

                // hidden input に選択されたカテゴリ ID を保存
                categoryIdInput.value = selectedCategories.join(',');
            });
        });

        // 画像プレビュー表示
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="プレビュー画像">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = "画像を選択してください";
            }
        });
    });
</script>
@endsection
