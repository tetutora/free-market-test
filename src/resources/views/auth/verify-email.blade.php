@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メールアドレスの確認</h1>
    <p>メールアドレスを確認するリンクをお送りしました。メールを確認し、アカウントを認証してください。</p>
    <p>
        <a href="{{ route('verification.resend') }}">認証リンクが届かない場合はこちらをクリックして再送する。</a>
    </p>
</div>
@endsection