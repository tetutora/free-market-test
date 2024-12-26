@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')

<h1>商品を出品する</h1>
<form action="{{ route('sell') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 商品画像 -->
    <div>
        <label for="image">商品画像</label>
        <input type="file" id="image" name="image" accept="image/*" required>
    </div>

    <!-- カテゴリー タグ -->
    <label for="category_id">カテゴリ</label>
    <div id="category-tags">
        @foreach($categories as $category)
            <div class="category-tag">
                <input type="checkbox" class="category-checkbox" id="category_{{ $category->id }}" value="{{ $category->id }}" name="category_ids[]">
                <label for="category_{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>

    <!-- 商品状態 -->
    <div>
        <label for="status_id">商品状態</label>
        <select id="status_id" name="status_id" required>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}">{{ $status->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- 商品名 -->
    <div>
        <label for="name">商品名</label>
        <input type="text" id="name" name="name" required>
    </div>

    <!-- 商品説明 -->
    <div>
        <label for="description">商品説明</label>
        <textarea id="description" name="description"></textarea>
    </div>

    <!-- 販売価格 -->
    <div>
        <label for="price">価格</label>
        <input type="number" id="price" name="price" required>
    </div>

    <!-- 出品ボタン -->
    <button type="submit">出品する</button>
</form>

@endsection

@section('css')
<style>
    #category-tags {
        display: flex;
        flex-wrap: wrap;
    }

    .category-tag {
        background-color: #f0f0f0;
        padding: 5px 10px;
        margin: 5px;
        border-radius: 3px;
        cursor: pointer;
    }

    .category-tag input {
        margin-right: 5px;
    }

    .category-tag:hover {
        background-color: #d3d3d3;
    }
</style>
@endsection

@section('js')
<script>
    // カテゴリ選択時に category_id に選ばれたカテゴリのIDを設定する
    document.querySelectorAll('.category-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            document.getElementById('category_id').value = selectedCategories.join(',');
        });
    });
</script>
@endsection
