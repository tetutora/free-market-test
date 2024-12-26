@extends('layouts.app')

@section
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')

<h1>{{ $product->name }}を購入する</h1>
<p>{{ $product->price }}円</p>
<form method="POST" action="{{ route('purchase.confirm', $product->id) }}">
    @csrf
    <button type="submit">購入手続き</button>
</form>


@endsection