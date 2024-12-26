<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/"><img src="{{ asset('images/logo.svg') }}" alt="アイコン" class="header__logo-icon"></a>
                <nav>
                    <ul class="header-nav">
                        @if (Auth::check())
                        <li class="header-nav__item">
                            <form class="header-nav__search" action="{{ url()->current() }}" method="GET">
                                <input type="text" name="search" placeholder="検索" class="header-nav__search-input" value="{{ request('search') }}">
                                <button type="submit" class="header-nav__search-button">検索</button>
                            </form>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__item">
                            <form class="form" action="/logout" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="{{ route('sell') }}">出品</a>
                        </li>
                    @endif
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>

<style>
/* PC (1400px - 1540px) */
@media screen and (max-width: 1540px) {
    .header {
        height: 80px;
        background-color: rgba(0, 0, 0, 1);
    }

    .header__logo-icon {
        padding: 20px 10px;
    }
}

/* タブレット (768px - 850px) */
@media screen and (max-width: 850px) {
    .header {
        height: 80px;
        background-color: rgba(0, 0, 0, 1);
    }

    .header__logo-icon {
        padding: 10px 10px;
    }
}

/* モバイル (480px 以下) */
@media screen and (max-width: 480px) {
    .header {
        height: 40px;
        background-color: rgba(0, 0, 0, 1);
    }

    .header__inner {
        margin: auto;
    }

    .header__logo-icon {
        width: 150px;
    }
}

.header {
    padding: 10px;
    background-color: rgba(0, 0, 0, 1);
}

/* ヘッダー内でロゴとナビゲーションを横並びにする */
.header__inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
}

.header-utilities {
    display: flex;
    align-items: center;
}

.header-nav {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    list-style: none;
}

.header-nav__item {
    margin-left: 20px;
}

.header-nav__link,
.header-nav__search-button {
    text-decoration: none;
    color: white;
    font-size: 16px;
    padding: 8px 12px;
}

.header-nav__search {
    display: flex;
    align-items: center;
}

.header-nav__search-input {
    padding: 5px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.header-nav__search-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.header-nav__search-button:hover {
    background-color: #0056b3;
}
</style>
