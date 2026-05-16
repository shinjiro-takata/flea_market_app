@extends('layouts.app')

@section('title', 'プロフィール')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <!-- プロフィール情報セクション -->
    <div class="profile-section">
        <div class="profile-header">
            @php
            $defaultProfileImageSrc = asset('images/default-profile.svg');
            $profileImageSrc = $user->profile_image
            ? (\Illuminate\Support\Str::startsWith($user->profile_image, ['http://', 'https://'])
            ? $user->profile_image
            : (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_image)
            ? asset('storage/' . $user->profile_image)
            : $defaultProfileImageSrc))
            : $defaultProfileImageSrc;
            @endphp
            <img src="{{ $profileImageSrc }}" alt="{{ $user->name }}" class="profile-image">
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p class="profile-email">{{ $user->email }}</p>
            </div>
            <a href="{{ route('mypage.profile') }}" class="edit-profile-button">
                プロフィールを編集
            </a>
        </div>
    </div>

    <!-- タブ切り替え -->
    <div class="tabs-section">
        <div class="tabs">
            <a href="{{ route('items.mypage', ['page' => 'sell']) }}"
                class="tab-button {{ $page === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('items.mypage', ['page' => 'buy']) }}"
                class="tab-button {{ $page === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        <!-- 出品した商品タブ -->
        <div class="tab-content {{ $page === 'sell' ? 'active' : '' }}">
            @if($sellItems->isEmpty())
            <p class="empty-message">出品した商品はありません</p>
            @else
            <div class="items-grid">
                @foreach($sellItems as $item)
                <div class="item-card">
                    <a href="{{ route('items.show', $item->id) }}" class="item-card__link">
                        @if($item->images->isNotEmpty())
                        @php
                        $imageSrc = \Illuminate\Support\Str::startsWith($item->images->first()->image_path, ['http://', 'https://'])
                        ? $item->images->first()->image_path
                        : asset('storage/' . $item->images->first()->image_path);
                        @endphp
                        <img src="{{ $imageSrc }}" alt="{{ $item->name }}" class="item-image">
                        @else
                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="item-image">
                        @endif
                        <div class="item-info">
                            <h3>{{ $item->name }}</h3>
                            <p class="item-price">¥{{ number_format($item->price) }}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- 購入した商品タブ -->
        <div class="tab-content {{ $page === 'buy' ? 'active' : '' }}">
            @if($buyItems->isEmpty())
            <p class="empty-message">購入した商品はありません</p>
            @else
            <div class="items-grid">
                @foreach($buyItems as $order)
                <div class="item-card">
                    <a href="{{ route('items.show', $order->item->id) }}" class="item-card__link">
                        @if($order->item->images->isNotEmpty())
                        @php
                        $imageSrc = \Illuminate\Support\Str::startsWith($order->item->images->first()->image_path, ['http://', 'https://'])
                        ? $order->item->images->first()->image_path
                        : asset('storage/' . $order->item->images->first()->image_path);
                        @endphp
                        <img src="{{ $imageSrc }}" alt="{{ $order->item->name }}" class="item-image">
                        @else
                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="item-image">
                        @endif
                        <div class="item-info">
                            <h3>{{ $order->item->name }}</h3>
                            <p class="item-price">¥{{ number_format($order->item->price) }}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endsection
    function switchTab(tabName) {