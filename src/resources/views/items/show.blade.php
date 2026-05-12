@extends('layouts.app')

@section('title', $item->name . ' | 商品詳細')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('scripts')
@endsection

@section('content')
<div class="container">
    <div class="content">
        <section>
            <div class="gallery">
                @if ($item->images->isNotEmpty())
                @foreach ($item->images as $image)
                @php
                $imageSrc = \Illuminate\Support\Str::startsWith($image->image_path, ['http://', 'https://'])
                ? $image->image_path
                : asset('storage/' . $image->image_path);
                @endphp
                <div class="gallery__item">
                    <img src="{{ $imageSrc }}" alt="{{ $item->name }}">
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
                @if (auth()->check())
                <form action="{{ route('like.toggle', $item->id) }}" method="POST" class="like-form">
                    @csrf
                    <button type="submit" class="like-button">
                        <img src="{{ $isLiked ? asset('images/icons/ハートロゴ_ピンク.png') : asset('images/icons/ハートロゴ_デフォルト.png') }}" alt="いいね" class="like-icon">
                        <span class="like-count">({{ $item->likes_count }})</span>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="like-button">
                    <img src="{{ asset('images/icons/ハートロゴ_デフォルト.png') }}" alt="いいね" class="like-icon">
                    <span class="like-count">({{ $item->likes_count }})</span>
                </a>
                @endif
                <span class="comment-count">
                    <img src="{{ asset('images/icons/ふきだしロゴ.png') }}" alt="コメント" class="comment-icon">
                    <span>コメント({{ $item->comments_count }})</span>
                </span>
            </div>
            <div class="actions">
                @if ($item->status === 'sold')
                <button class="btn btn--disabled" disabled>売り切れです</button>
                @else
                <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn">購入手続きへ</a>
                @endif
            </div>

            <div class="seller-box">
                <div class="label">出品者</div>
                <div class="value">{{ optional($item->seller)->name ?? '出品者未設定' }}</div>
            </div>

            <div class="description-box">
                <h2>商品説明</h2>
                <p class="description-box__text">{{ $item->description }}</p>
            </div>

            <div class="info-box">
                <h2>商品の情報</h2>
                <div class="info-row">
                    <div class="info-label">カテゴリー</div>
                    <div class="info-value">
                        @if ($item->categories->isNotEmpty())
                        @foreach ($item->categories as $category)
                        <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                        @else
                        <span>未設定</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">商品の状態</div>
                    <div class="info-value">{{ $item->condition ?? '未設定' }}</div>
                </div>
            </div>

            <div class="comments-section">
                <h2>コメント({{ $item->comments_count }})</h2>

                @foreach($item->comments as $comment)
                <div class="comment-item">
                    <div class="comment-user">
                        {{ $comment->user->name }}
                    </div>
                    <div class="comment-content">
                        {{ $comment->comment }}
                    </div>
                </div>
                @endforeach
            </div>

            @if(auth()->check())
            <form action="{{ route('comment.store', $item->id) }}" method="POST" class="comment-form">
                @csrf
                <textarea name="comment" placeholder="コメントを入力"></textarea>
                @error('comment')
                <div class="error-message">{{ $message }}</div>
                @enderror
                <button type="submit" class="comment-form__button">コメントを送信する</button>
            </form>
            @endif
        </section>
    </div>
</div>
@endsection