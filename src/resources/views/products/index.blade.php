@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<h1>商品一覧</h1>

<div class="product-list">
    @foreach($products as $product)
        <div class="product-item">
            <!-- 画像をクリックで詳細ページに遷移 -->
            <a href="{{ route('product.show', $product->id) }}">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 150px; height: auto;">
            </a>
            <p>{{ $product->name }}</p>
        </div>
    @endforeach
</div>

@endsection
