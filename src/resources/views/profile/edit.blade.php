@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="profile-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- プロフィール画像 -->
        <div class="form__group-photo">
    <div id="image-preview" style="{{ $profile_picture ? '' : 'display:none;' }}">
        <img id="image-preview-img" src="{{ $profile_picture }}" alt="プロフィール画像" width="200" height="200" />
    </div>
    <div class="form__input">
        <label for="profile_picture" class="custom-file-label">画像を選択</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)" style="display: none;">
    </div>
    <div class="form__error">
        @error('profile_picture')
            {{ $message }}
        @enderror
    </div>
</div>


        <!-- ユーザー名 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input class="form__input-text" type="text" name="name" value="{{ old('name', $profile->name) }}" />
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 郵便番号 -->
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input class="form__input-text" type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $profile->zipcode) }}" pattern="\d{3}-\d{4}|\d{7}" maxlength="8" placeholder="123-4567 または 1234567" />
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
        
        preview.style.display = 'block'; // プレビューを表示
        previewImg.src = e.target.result; // 画像を表示
    };

    if (file) {
        reader.readAsDataURL(file); // 画像を読み込む
    }
}
</script>
@endsection
