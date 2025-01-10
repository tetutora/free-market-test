<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="アイコン" class="header__logo-icon">
            </a>
            <nav class="header__nav">
                <ul class="header-nav">
                    <li class="header-nav__item">
                        <form class="header-nav__search" action="{{ url()->current() }}" method="GET">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="なにをお探しですか？" 
                                class="header-nav__search-input" 
                                value="{{ request('search') }}">
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

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('.header-nav__search-input');
        const searchForm = document.querySelector('.header-nav__search');
        let debounceTimeout;

        if (searchInput && searchForm) {
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            });
        }
    });
</script>