<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Flea Market')</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('styles')
</head>

<body>
    <header class="app-header">
        <div class="app-header__inner">
            <a href="{{ route('items.index') }}" class="app-header__logo">COACHTECH</a>

            <form action="{{ route('items.index') }}" method="get" class="app-header__search" role="search">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="なにをお探しですか？"
                    aria-label="商品検索">
            </form>

            <nav class="app-header__nav">
                @if (auth()->check())
                <a href="#">ログアウト</a>
                @else
                <a href="{{ route('login') }}">ログイン</a>
                @endif
                <a href="{{ route('mypage') }}">マイページ</a>
                <a href="{{ route('items.sell') }}" class="app-header__sell">出品</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>