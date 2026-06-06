@extends('layouts.app')

@section('title', '商品一覧')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="tabs">
        <a href="{{ route('items.index', $q ? ['q' => $q] : []) }}" class="tabs__link {{ $tab === 'recommended' ? 'is-active' : '' }}">おすすめ</a>
        <a href="{{ route('items.index', ['tab' => 'mylist'] + ($q ? ['q' => $q] : [])) }}" class="tabs__link {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
    </div>

    @if ($showLoginMessage)
    <div class="notice">
        マイリストはログイン後に表示されます。
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
                <img src="{{ $item->first_image_src }}" alt="{{ $item->name }}">

                @if ($item->status === 'sold')
                <span class="card__badge">Sold</span>
                @endif
            </div>
            <div class="card__body">
                <h2 class="card__title">{{ $item->name }}</h2>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection