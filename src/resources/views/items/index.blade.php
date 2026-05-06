@extends('layouts.app')

@section('title', '商品一覧')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="tabs">
        <a href="{{ route('items.index', $q ? ['q' => $q] : []) }}" class="{{ $tab === 'recommended' ? 'is-active' : '' }}">おすすめ</a>
        <a href="{{ route('items.index', ['tab' => 'mylist'] + ($q ? ['q' => $q] : [])) }}" class="{{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
    </div>

    @if ($showLoginMessage)
    <div class="notice">
        マイリストはログイン後に表示する想定です。今は認証画面が未実装なので、一覧は空表示にしています。
    </div>
    @endif

    @if ($items->isEmpty())
    <div class="empty">
        表示できる商品がまだありません。
    </div>
    @else
    <div class="grid">
        @foreach ($items as $item)
        <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="card">
            <div class="card__image">
                @if ($item->images->isNotEmpty())
                @php
                $firstImagePath = $item->images->first()->image_path;
                $firstImageSrc = \Illuminate\Support\Str::startsWith($firstImagePath, ['http://', 'https://'])
                ? $firstImagePath
                : asset('storage/' . $firstImagePath);
                @endphp
                <img src="{{ $firstImageSrc }}" alt="{{ $item->name }}">
                @else
                <span>画像なし</span>
                @endif

                @if ($item->status === 'sold')
                <span class="card__badge">Sold</span>
                @endif
            </div>
            <div class="card__body">
                <h2 class="card__title">{{ $item->name }}</h2>
                <div class="card__price">￥{{ number_format($item->price) }}</div>
                <div class="card__meta">
                    <span>{{ optional($item->seller)->name ?? '出品者未設定' }}</span>
                    <span>いいね {{ $item->likes_count }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection