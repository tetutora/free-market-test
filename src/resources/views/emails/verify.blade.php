@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メールアドレスの確認</h1>
<p>こちらのリンクをクリックしてメールアドレスを確認してください：</p>
<a href="{{ url('verification/verify/' . $user->id . '/' . sha1($user->email)) }}">メールアドレスを確認する</a>

</div>
@endsection