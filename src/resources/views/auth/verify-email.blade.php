@extends('layouts.app')

@section('content')
<div class="alert alert-info">
    メール認証が完了していません。<br>
    認証メールを確認し、リンクをクリックしてください。
</div>
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<form method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit" class="btn btn-primary">認証メールを再送する</button>
</form>
@endsection
