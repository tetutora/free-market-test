@extends('layouts.app')

@section('content')
<div class="address-edit-container">
    <h2>住所を編集</h2>
    <form action="{{ route('profile.address.update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->address->postal_code) }}" required>
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address->address) }}" required>
        </div>

        <button type="submit">住所を更新</button>
    </form>
</div>
@endsection
