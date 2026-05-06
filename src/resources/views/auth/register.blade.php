@extends('layouts.app')

@section('content')
<div class="container">
    <h1>会員登録</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">登録する</button>
    </form>
    <p>ログインは<a href="{{ route('login') }}">こちら</a></p>
</div>
@endsection