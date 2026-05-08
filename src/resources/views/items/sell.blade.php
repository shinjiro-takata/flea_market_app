@extends('layouts.app')

@section('title', '商品の出品')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="sell-page">
    <div class="sell-container">
        <h1 class="sell-title">商品の出品</h1>

        @if ($errors->any())
        <div class="error-box">
            <p class="error-title">入力内容を確認してください。</p>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('exhibition.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <section class="sell-section">
                <h2 class="sell-label">商品画像</h2>
                <div class="image-box">
                    <input name="image" type="file" class="file-input" accept="image/*">
                    <label for="image" class="image-select-btn" onclick="document.querySelector('input[name=image]').click()" style="cursor: pointer;">画像を選択する</label>
                </div>
            </section>

            <section class="sell-section">
                <h2 class="sell-section-title">商品の詳細</h2>

                <div class="field-block">
                    <h3 class="sell-label">カテゴリー</h3>
                    <div class="category-chips">
                        @foreach ($categories as $category)
                        <label class="chip-label">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                @if (in_array($category->id, old('categories', []))) checked @endif>
                            <span class="chip">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="field-block">
                    <label for="condition" class="sell-label">商品の状態</label>
                    <select id="condition" name="condition" class="input">
                        <option value="" {{ old('condition') ? '' : 'selected' }}>選択してください</option>
                        <option value="良好" {{ old('condition') === '良好' ? 'selected' : '' }}>良好</option>
                        <option value="目立った傷や汚れなし" {{ old('condition') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="やや傷や汚れあり" {{ old('condition') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="状態が悪い" {{ old('condition') === '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
                    </select>
                </div>
            </section>

            <section class="sell-section">
                <h2 class="sell-section-title">商品名と説明</h2>

                <div class="field-block">
                    <label for="name" class="sell-label">商品名</label>
                    <input id="name" name="name" type="text" class="input" value="{{ old('name') }}">
                </div>

                <div class="field-block">
                    <label for="brand_name" class="sell-label">ブランド名</label>
                    <input id="brand_name" name="brand_name" type="text" class="input" value="{{ old('brand_name') }}">
                </div>

                <div class="field-block">
                    <label for="description" class="sell-label">商品の説明</label>
                    <textarea id="description" name="description" class="textarea">{{ old('description') }}</textarea>
                </div>

                <div class="field-block">
                    <label for="price" class="sell-label">販売価格</label>
                    <div class="price-wrap">
                        <span class="price-mark">¥</span>
                        <input id="price" name="price" type="number" min="1" class="input price-input" value="{{ old('price') }}">
                    </div>
                </div>
            </section>

            <div class="submit-area">
                <button type="submit" class="submit-btn">出品する</button>
            </div>
        </form>
    </div>
</div>
@endsection