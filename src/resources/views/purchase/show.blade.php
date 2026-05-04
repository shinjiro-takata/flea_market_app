@extends('layouts.app')

@section('title', '購入画面')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <h1>購入確認</h1>

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <!-- 商品情報セクション -->
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #ddd;">
            <h2 style="margin-top: 0;">商品情報</h2>

            <div style="display: flex; gap: 20px;">
                @if($item->images->isNotEmpty())
                <div style="width: 200px; height: 200px; background: #f5f5f5; border-radius: 8px; overflow: hidden;">
                    @php
                    $imageSrc = \Illuminate\Support\Str::startsWith($item->images->first()->image_path, ['http://', 'https://'])
                    ? $item->images->first()->image_path
                    : asset('storage/' . $item->images->first()->image_path);
                    @endphp
                    <img src="{{ $imageSrc }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                @endif

                <div>
                    <h3 style="margin: 0 0 10px 0;">{{ $item->name }}</h3>
                    <p style="color: #666; margin: 10px 0;">ブランド: {{ $item->brand_name ?? '未設定' }}</p>
                    <p style="font-size: 24px; font-weight: bold; color: #e74c3c; margin: 10px 0;">￥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        <!-- 配送先セクション -->
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #ddd;">
            <h2 style="margin-top: 0;">配送先</h2>

            @if($address)
            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                <p style="margin: 5px 0;">〒{{ $address->postal_code }}</p>
                <p style="margin: 5px 0;">{{ $address->prefecture }} {{ $address->municipality }} {{ $address->street_address }}</p>
            </div>
            <a href="{{ route('purchase.address', $item->id) }}" style="color: #3498db; text-decoration: none; margin-top: 10px; display: inline-block;">
                配送先を変更する
            </a>
            @else
            <p style="color: #999;">配送先が登録されていません</p>
            <a href="{{ route('purchase.address', $item->id) }}" style="color: #3498db; text-decoration: none;">
                配送先を追加する
            </a>
            @endif
        </div>

        <!-- 購入フォーム -->
        <form action="{{ route('purchase.store', $item->id) }}" method="POST">
            @csrf
            <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

            <!-- 支払い方法セクション -->
            <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #ddd;">
                <h2 style="margin-top: 0;">支払い方法</h2>

                <select name="payment_method" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="credit_card">クレジットカード</option>
                    <option value="convenience_store">コンビニ支払い</option>
                </select>

                <div style="margin-top: 15px; background: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <p style="margin: 0;">小計: <strong>￥{{ number_format($item->price) }}</strong></p>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #e74c3c; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                購入する
            </button>
        </form>
    </div>
</div>
@endsection
