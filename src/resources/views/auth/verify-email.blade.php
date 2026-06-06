@extends('layouts.auth')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')

<div class="auth-form">
    @if (session('resent'))
    <div class="verify-email__success-message">
        認証メールを再送信しました。<br>
        メールをご確認ください。
    </div>
    @endif

    <p class="verify-email__description">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <form method="POST" action="{{ route('verification.quick') }}" class="verify-email__form-primary">
        @csrf
        <button type="submit" class="verify-email__submit-primary">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="verify-email__submit-secondary">認証メールを再送する</button>
    </form>
</div>
@endsection