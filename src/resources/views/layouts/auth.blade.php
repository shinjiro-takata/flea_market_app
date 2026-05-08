<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Flea Market')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/auth.css') }}">
    @yield('styles')
</head>

<body>
    <header class="auth-header">
        <div class="auth-header__inner">
            <a href="{{ route('items.index') }}" class="auth-header__logo">
                <img src="{{ asset('images/logo/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <div class="auth-form-wrapper">
                @yield('content')
            </div>
        </div>
    </main>

    @yield('scripts')
</body>

</html>