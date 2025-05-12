<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('css')
    @yield('js')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="アイコン" class="header__logo-icon">
            </a>
            <nav class="header__nav">
                <ul class="header-nav">
                    <li class="header-nav__item __header-nav__search-container">
                        <form class="header-nav__search" action="{{ url()->current() }}" method="GET">
                            <input
                                type="text"
                                name="search"
                                placeholder="なにをお探しですか？"
                                class="header-nav__search-input"
                                value="{{ request('search') }}">
                        </form>
                    </li>

                    @auth
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__item">
                            <form class="form" action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                    @endauth

                    @guest
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="{{ route('login') }}">ログイン</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="{{ route('register') }}">会員登録</a>
                        </li>
                    @endguest

                    <li class="header-nav__item">
                        <a class="header-nav__link header-nav__link--highlight" href="{{ route('sell') }}">出品</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
        @yield('js')
    </main>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('.header-nav__search-input');
        const searchForm = document.querySelector('.header-nav__search');
        let debounceTimeout;

        if (searchInput && searchForm) {
            if (searchInput.value) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('search', searchInput.value);  // 'search'パラメータをセット
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);
            }

            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            });
        }
    });
</script>