@extends('layouts.app')

@section('title', '購入画面')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <h1>購入確認</h1>

    <div class="purchase-card">
        <!-- 商品情報セクション -->
        <div class="purchase-section">
            <h2>商品情報</h2>

            <div class="item-info-row">
                @if($item->images->isNotEmpty())
                <div class="item-image-container">
                    @php
                    $imageSrc = \Illuminate\Support\Str::startsWith($item->images->first()->image_path, ['http://', 'https://'])
                    ? $item->images->first()->image_path
                    : asset('storage/' . $item->images->first()->image_path);
                    @endphp
                    <img src="{{ $imageSrc }}" alt="{{ $item->name }}">
                </div>
                @endif

                <div class="item-details">
                    <h3>{{ $item->name }}</h3>
                    <p class="item-brand">ブランド: {{ $item->brand_name ?? '未設定' }}</p>
                    <p class="item-price">￥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        <!-- 配送先セクション -->
        <div class="purchase-section">
            <h2>配送先</h2>

            @if($address)
            <div class="address-box">
                <p>〒{{ $address->postal_code }}</p>
                <p>{{ $address->prefecture }} {{ $address->municipality }} {{ $address->street_address }}</p>
            </div>
            <a href="{{ route('purchase.address', $item->id) }}" class="address-link">
                配送先を変更する
            </a>
            @else
            <p class="address-empty">配送先が登録されていません</p>
            <a href="{{ route('purchase.address', $item->id) }}" class="address-link">
                配送先を追加する
            </a>
            @endif
        </div>

        <!-- 購入フォーム -->
        <form action="{{ route('purchase.store', $item->id) }}" method="POST">
            @csrf
            <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

            <!-- 支払い方法セクション -->
            <div class="purchase-section">
                <h2>支払い方法</h2>

                <select name="payment_method" class="payment-method-select">
                    <option value="credit_card">クレジットカード</option>
                    <option value="convenience_store">コンビニ支払い</option>
                </select>

                <div class="subtotal-box">
                    <p>小計: <strong>￥{{ number_format($item->price) }}</strong></p>
                </div>
            </div>

            <button type="submit" class="purchase-button">
                購入する
            </button>
        </form>
    </div>
</div>
@endsection