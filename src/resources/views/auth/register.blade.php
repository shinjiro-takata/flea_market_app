@extends('layouts.auth')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<h1 class="auth-form__title">会員登録</h1>

<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <div class="auth-form__group">
        <label for="name" class="auth-form__label">ユーザー名</label>
        <input type="text" class="auth-form__input @error('name') auth-form__input--error @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
        <span class="auth-form__error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="auth-form__group">
        <label for="email" class="auth-form__label">メールアドレス</label>
        <input type="email" class="auth-form__input @error('email') auth-form__input--error @enderror" id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
        <span class="auth-form__error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="auth-form__group">
        <label for="password" class="auth-form__label">パスワード</label>
        <input type="password" class="auth-form__input @error('password') auth-form__input--error @enderror" id="password" name="password" required>
        @error('password')
        <span class="auth-form__error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="auth-form__group">
        <label for="password_confirmation" class="auth-form__label">確認用パスワード</label>
        <input type="password" class="auth-form__input @error('password_confirmation') auth-form__input--error @enderror" id="password_confirmation" name="password_confirmation" required>
        @error('password_confirmation')
        <span class="auth-form__error-message">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="auth-form__submit">登録する</button>
</form>

<p class="auth-form__link-text">
    <a href="{{ route('login') }}" class="auth-form__link">ログインはこちら</a>
</p>
@endsection