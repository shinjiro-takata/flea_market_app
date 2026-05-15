@extends('layouts.app')

@section('title', '住所の変更')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}">
@endsection

@section('content')
<div class="address-change-container">
    <h1 class="address-change-container__title">住所の変更</h1>

    <div class="address-form-card">
        <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
            @csrf

            <!-- 郵便番号 -->
            <div class="form-group">
                <label for="postal_code" class="form-label">郵便番号</label>
                <input
                    type="text"
                    id="postal_code"
                    name="postal_code"
                    class="form-input"
                    placeholder="例: 123-4567"
                    value="{{ old('postal_code', $address?->postal_code ?? '') }}"
                    required>
                @error('postal_code')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- 住所 -->
            <div class="form-group">
                <label for="prefecture" class="form-label">住所</label>
                <input
                    type="text"
                    id="prefecture"
                    name="prefecture"
                    class="form-input"
                    placeholder="例: 東京都渋谷区 1-2-3"
                    value="{{ old('prefecture', $address?->prefecture ?? '') }}"
                    required>
                @error('prefecture')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- 建物名 -->
            <div class="form-group">
                <label for="street_address" class="form-label">建物名</label>
                <input
                    type="text"
                    id="street_address"
                    name="street_address"
                    class="form-input"
                    placeholder="例: ビル名 101"
                    value="{{ old('street_address', $address?->street_address ?? '') }}">
                @error('street_address')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- 更新ボタン -->
            <button type="submit" class="update-button">
                更新する
            </button>
        </form>
    </div>
</div>
@endsection