@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products/create.css') }}">
@endsection

@section('content')
<div class="create-product">
    <h1 class="center-title">商品の出品</h1>
    <form action="{{ route('sell') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="image" class="form-label"><strong>商品画像</strong></label>
            <div id="image-preview" class="image-preview">画像を選択してください</div>
            <label for="image" class="custom-file-label">画像を選択</label>
            <input type="file" id="image" name="image" accept="image/*" class="image-upload">
            <div class="form-error">
                @error('image')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="categories" class="form-label"><strong>カテゴリ</strong></label>
            <div id="categories" class="categories">
                @foreach($categories as $category)
                    <div class="category-tag" data-id="{{ $category->id }}">{{ $category->name }}</div>
                @endforeach
            </div>
            <input type="hidden" id="category_id" name="category_id" value="{{ old('category_id') }}">
            <div class="form-error">
                @error('category_id')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="status" class="form-label"><strong>商品状態</strong></label>
            <select id="status" name="status" class="form-input">
                <option value="">-- 選択してください --</option>
                <option value="良好" {{ old('status') == '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('status') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('status') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="状態が悪い" {{ old('status') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
            </select>
            <div class="form-error">
                @error('status')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="form-label"><strong>商品名</strong></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input">
            <div class="form-error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="brand_name" class="form-label"><strong>ブランド名</strong></label>
            <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" class="form-input">
            <div class="form-error">
                @error('brand_name')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label"><strong>商品説明</strong></label>
            <textarea id="description" name="description" class="form-input-text">{{ old('description') }}</textarea>
            <div class="form-error">
                @error('description')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="price" class="form-label"><strong>価格</strong></label>
            <input type="number" id="price" name="price" value="{{ old('price') }}" class="form-input">
            <div class="form-error">
                @error('price')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="submit-btn">出品する</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryTags = document.querySelectorAll('.category-tag');
        const categoryIdInput = document.getElementById('category_id');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');

        let selectedCategories = [];

        categoryTags.forEach(tag => {
            tag.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-id');

                if (selectedCategories.includes(categoryId)) {
                    selectedCategories = selectedCategories.filter(id => id !== categoryId);
                    this.classList.remove('selected');
                } else {
                    selectedCategories.push(categoryId);
                    this.classList.add('selected');
                }
                categoryIdInput.value = selectedCategories.join(',');
            });
        });

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
