@extends('layouts.app')

@section('title', $item->name . ' | 商品詳細')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="content">
        <section>
            <div class="gallery">
                @if ($item->images->isNotEmpty())
                @foreach ($item->images as $image)
                <div class="gallery__item">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->name }}">
                </div>
                @endforeach
                @else
                <div class="gallery__empty">画像が登録されていません</div>
                @endif
            </div>
        </section>

        <section>
            <h1 class="item-title">{{ $item->name }}</h1>
            <p class="brand-name">ブランド名 {{ $item->brand_name ?? '未設定' }}</p>

            <p class="price">￥{{ number_format($item->price) }}</p>

            <div class="chips">
                <span class="chip">{{ $item->status === 'sold' ? 'Sold' : '販売中' }}</span>
                <span class="chip">いいね {{ $item->likes_count }}</span>
                <span class="chip">{{ $isLiked ? 'あなたはいいね済み' : '未いいね' }}</span>
            </div>

            <div class="seller-box">
                <div class="label">出品者</div>
                <div class="value">{{ optional($item->seller)->name ?? '出品者未設定' }}</div>
            </div>

            <div class="description-box">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>

            <p class="condition">商品の状態 {{ $item->condition ?? '未設定' }}</p>

            <div class="actions">
                @if ($item->status === 'sold')
                <button class="btn btn--disabled" disabled>売り切れです</button>
                @else
                <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn">購入手続きへ</a>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection