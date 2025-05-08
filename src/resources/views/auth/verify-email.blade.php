@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メールアドレスの確認</h1>
    <p>メールアドレスを確認するリンクをお送りしました。メールを確認してください。</p>
    <a href="https://mailtrap.io/inboxes">認証画面</a>
    <p>
        <a href="{{ route('verification.resend') }}">認証リンクが届かない場合はこちらをクリックして再送する。</a>
    </p>
</div>
@endsection