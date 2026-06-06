@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h1 class="profile-container__title">プロフィール設定</h1>

    <div class="profile-card">
        <!-- プロフィール画像セクション -->
        <div class="profile-image-section">
            <img src="{{ $user->profile_image_src }}" alt="プロフィール画像" class="profile-image" id="profile-image-preview">
        </div>

        <!-- プロフィール編集フォーム -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf

            <!-- プロフィール画像 -->
            <div class="form-group">
                <label for="profile_image" class="form-label">プロフィール画像</label>
                <input
                    type="file"
                    id="profile_image"
                    name="profile_image"
                    class="form-input"
                    accept="image/*">
                @error('profile_image')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- ユーザー名 -->
            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    value="{{ old('name', $user->name) }}"
                    required>
                @error('name')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

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

            <!-- 都道府県 -->
            <div class="form-group">
                <label for="prefecture" class="form-label">都道府県</label>
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
                    placeholder="例: 1-2-3 ビル名 101"
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