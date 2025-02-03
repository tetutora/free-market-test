@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="profile-form__heading">
        <h2>住所の変更</h2>
    </div>
    <form class="form" action="{{ route('profile.address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item_id }}">

        <!-- 郵便番号 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input class="form__input-text" type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $profile->zipcode) }}" pattern="\d{3}-\d{4}|\d{7}" maxlength="8" placeholder="123-4567" />
                </div>
                <div class="form__error">
                    @error('zipcode')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 住所 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input class="form__input-text" type="text" name="address" value="{{ old('address', $profile->address) }}" />
                </div>
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input class="form__input-text" type="text" name="building" value="{{ old('building', $profile->building) }}" />
                </div>
                <div class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection
