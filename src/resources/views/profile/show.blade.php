@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/show.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="profile-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form__group-photo">
        <div id="image-preview" style="display:none;">
            <img name="profile_picture" id="image-preview-img" src="#" alt="プロフィール画像" width="200" height="200" />
        </div>
        <div class="form__input">
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)">
        </div>
        <div class="form__error">
            @error('profile_picture')
                {{ $message }}
            @enderror
        </div>
    </div>
    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">ユーザー名</span>
        </div>
        <div class="form__group-content">
            <div class="form__input">
                <input class="form__input-text" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" />
            </div>
            <div class="form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>
    </div>
    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">郵便番号</span>
        </div>
        <div class="form__group-content">
            <div class="form__input">
                <input class="form__input-text" type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', Auth::user()->zipcode) }}" pattern="\d{3}-\d{4}" maxlength="8" placeholder="123-4567">
            </div>
            <div class="form__error">
                @error('zipcode')
                    {{ $message }}
                @enderror
            </div>
        </div>
    </div>
    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">住所</span>
        </div>
        <div class="form__group-content">
            <div class="form__input">
                <input class="form__input-text" type="text" name="address" value="{{ old('address', Auth::user()->address) }}" />
            </div>
            <div class="form__error">
                @error('address')
                    {{ $message }}
                @enderror
            </div>
        </div>
    </div>
    <div class="form__group">
        <div class="form__group-title">
            <span class="form__label--item">建物名</span>
        </div>
        <div class="form__group-content">
            <div class="form__input">
                <input class="form__input-text" type="text" name="building" value="{{ old('building', Auth::user()->building) }}" />
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

<script>
function previewImage(event) {
    var file = event.target.files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
        var preview = document.getElementById('image-preview');
        var previewImg = document.getElementById('image-preview-img');
        preview.style.display = 'block';
        previewImg.src = e.target.result;
    };
    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
