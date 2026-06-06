@extends('layouts.auth')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')

<h1 class="auth-form__title">ログイン</h1>

<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    @error('email')
    <div class="auth-form__error">{{ $message }}</div>
    @enderror

    <div class="auth-form__group">
        <label for="email" class="auth-form__label">メールアドレス</label>
        <input type="email" class="auth-form__input @error('email') auth-form__input--error @enderror" id="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div class="auth-form__group">
        <label for="password" class="auth-form__label">パスワード</label>
        <input type="password" class="auth-form__input @error('password') auth-form__input--error @enderror" id="password" name="password" required>
        @error('password')
        <span class="auth-form__error-message">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="auth-form__submit">ログインする</button>
</form>

<p class="auth-form__link-text">
    <a href="{{ route('register') }}" class="auth-form__link">会員登録はこちら</a>
</p>
@endsection