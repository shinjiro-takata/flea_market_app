@extends('layouts.app')

@section('content')
<div class="container">
    <h1>ログイン</h1>
    @if ($errors->has('email') && $errors->first('email') === 'These credentials do not match our records.')
    <div class="error">ログイン情報が登録されていません</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
            @if ($message !== 'These credentials do not match our records.')
            <div class="error">{{ $message }}</div>
            @endif
            @enderror
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">ログインする</button>
    </form>
    <a href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection