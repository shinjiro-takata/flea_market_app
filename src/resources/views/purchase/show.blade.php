@extends('layouts.app')

@section('title', '購入画面')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
@endsection

@section('content')
<div class="purchase-container">

    <div class="purchase-card">
        <!-- 左側：商品情報、配送先、支払い方法 -->
        <div class="purchase-main">
            <!-- 商品情報セクション -->
            <div class="purchase-section">

                <div class="item-info-row">
                    @if($item->images->isNotEmpty())
                    <div class="item-image-container">
                        <img src="{{ $item->first_image_src }}" alt="{{ $item->name }}">
                    </div>
                    @endif

                    <div class="item-details">
                        <h3>{{ $item->name }}</h3>
                        <p class="item-details__text item-price">￥{{ number_format($item->price) }}</p>
                    </div>
                </div>
            </div>

            <!-- 支払い方法セクション -->
            <div class="purchase-section">
                <h2>支払い方法</h2>

                <form action="{{ route('purchase.setPayment', $item->id) }}" method="POST" class="payment-form-inline">
                    @csrf
                    <select class="payment-select" name="payment_method">
                        <option value="convenience_store" {{ $paymentMethod === 'convenience_store' ? 'selected' : '' }}>コンビニ支払い</option>
                        <option value="credit_card" {{ $paymentMethod === 'credit_card' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                    <button type="submit" class="payment-button">選択</button>
                </form>
            </div>

            <!-- 配送先セクション -->
            <div class="purchase-section">
                <div class="purchase-section__header">
                    <h2>配送先</h2>
                    <a href="{{ route('purchase.address', $item->id) }}" class="address-link">
                        変更する
                    </a>
                </div>

                @if($address)
                <div class="address-box">
                    <p class="address-box__text">〒{{ $address->postal_code }}</p>
                    <p class="address-box__text">{{ $address->prefecture }} {{ $address->street_address }}</p>
                </div>
                @else
                <p class="address-empty">配送先が登録されていません</p>
                @endif
            </div>
        </div>

        <!-- 右側：金額詳細と購入ボタン -->
        <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="purchase-form" class="purchase-sidebar">
            @csrf
            <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

            <div class="price-summary">
                <div class="price-summary__row">
                    <span class="price-summary__label">商品代金</span>
                    <span class="price-summary__value">￥{{ number_format($item->price) }}</span>
                </div>
                <div class="price-summary__row">
                    <span class="price-summary__label">支払い方法</span>
                    <span class="price-summary__value">{{ $paymentMethod === 'credit_card' ? 'カード支払い' : 'コンビニ払い' }}</span>
                </div>
            </div>

            <button type="submit" class="purchase-button">
                購入する
            </button>
        </form>
    </div>
</div>

@endsection