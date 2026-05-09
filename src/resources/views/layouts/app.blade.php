<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Flea Market')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('styles')
</head>

<body>
    <header class="app-header">
        <div class="app-header__inner">
            <a href="{{ route('items.index') }}" class="app-header__logo">
                <img src="{{ asset('images/logo/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>

            <form action="{{ route('items.index') }}" method="get" class="app-header__search" role="search">
                <input
                    type="text"
                    class="app-header__search__input"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="なにをお探しですか？"
                    aria-label="商品検索">
            </form>

            <nav class="app-header__nav">
                @if (auth()->check())
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="app-header__nav__link">ログイン</a>
                @endif
                <a href="{{ route('items.mypage') }}" class="app-header__nav__link">マイページ</a>
                <a href="{{ route('items.sell') }}" class="app-header__sell">出品</a>
            </nav>
        </div>
    </header>

    <main>
        @if ($message = session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; margin: 10px 0; border-radius: 4px; text-align: center;">
            {{ $message }}
        </div>
        @endif

        @if ($message = session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; margin: 10px 0; border-radius: 4px; text-align: center;">
            {{ $message }}
        </div>
        @endif

        @yield('content')
    </main>
    @yield('scripts')
</body>

</html>