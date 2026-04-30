@extends('layouts.app')

@section('title', $title)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pages/screen.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="card">
        <h1>{{ $title }}</h1>

        <div class="label">パス</div>
        <div class="value">{{ $path }}</div>

        <div class="label">この画面の役割</div>
        <div class="value">{{ $description }}</div>

        <div class="label">今受け取っている値</div>
        <div class="value">
            @if (empty($state))
            なし
            @else
            <ul>
                @foreach ($state as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
                @endforeach
            </ul>
            @endif
        </div>

        <div class="label">確認用リンク</div>
        <div class="value">
            <ul>
                <li><a href="{{ route('items.index') }}">トップ画面</a></li>
                <li><a href="{{ route('items.index', ['tab' => 'mylist']) }}">トップ画面（マイリスト）</a></li>
                <li><a href="{{ route('register') }}">会員登録画面</a></li>
                <li><a href="{{ route('login') }}">ログイン画面</a></li>
                <li><a href="{{ route('items.show', ['item_id' => 1]) }}">商品詳細画面</a></li>
                <li><a href="{{ route('purchase.show', ['item_id' => 1]) }}">商品購入画面</a></li>
                <li><a href="{{ route('purchase.address', ['item_id' => 1]) }}">送付先住所変更画面</a></li>
                <li><a href="{{ route('items.sell') }}">商品出品画面</a></li>
                <li><a href="{{ route('mypage') }}">プロフィール画面</a></li>
                <li><a href="{{ route('mypage', ['page' => 'buy']) }}">プロフィール画面_購入した商品一覧</a></li>
                <li><a href="{{ route('mypage', ['page' => 'sell']) }}">プロフィール画面_出品した商品一覧</a></li>
                <li><a href="{{ route('mypage.profile') }}">プロフィール編集画面</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection