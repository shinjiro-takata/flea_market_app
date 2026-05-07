<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::find($item_id);
        $address = auth()->user()->addresses()->first();

        // アドレスが登録されていない場合はプロフィール画面へ
        if (!$address) {
            return redirect()->route('mypage.profile')->with('error', '配送先を登録してください');
        }

        return view('purchase.show', [
            'item' => $item,
            'address' => $address,
        ]);
    }

    public function store(Request $request, $item_id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $item = Item::find($item_id);
        $address = Address::find($request->input('address_id'));
        $paymentMethod = $request->input('payment_method', 'credit_card');

        if (!$item || !$address) {
            return redirect()->route('items.index')->with('error', '商品または住所が見つかりません');
        }

        try {
            // 支払い方法に応じて payment_method_types を設定
            $paymentMethodTypes = [];
            if ($paymentMethod === 'credit_card') {
                $paymentMethodTypes = ['card'];
            } elseif ($paymentMethod === 'convenience_store') {
                $paymentMethodTypes = ['konbini'];
            } else {
                $paymentMethodTypes = ['card']; // デフォルト
            }

            // Stripeセッション作成
            $session = StripeSession::create([
                'payment_method_types' => $paymentMethodTypes,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price,
                        'product_data' => [
                            'name' => $item->name,
                            'images' => $item->images->isNotEmpty() ? [
                                \Illuminate\Support\Str::startsWith($item->images->first()->image_path, ['http://', 'https://'])
                                    ? $item->images->first()->image_path
                                    : asset('storage/' . $item->images->first()->image_path)
                            ] : [],
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url' => route('purchase.show', ['item_id' => $item->id]),
                'metadata' => [
                    'item_id' => $item->id,
                    'buyer_id' => auth()->id(),
                    'address_id' => $address->id,
                    'payment_method' => $paymentMethod,
                ],
            ]);

            // Stripe Checkoutページにリダイレクト
            return redirect($session->url);
        } catch (\Exception $e) {
            \Log::error('Stripe session error: ' . $e->getMessage());
            return redirect()->route('items.index')->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    public function success($item_id)
    {
        // セッションからメタデータを取得してOrderを作成
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $item = Item::find($item_id);
        $user = auth()->user();

        if (!$item) {
            return redirect()->route('items.index')->with('error', '商品が見つかりません');
        }

        // 既に注文が作成済みか確認
        $existingOrder = Order::where('item_id', $item->id)
            ->where('buyer_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$existingOrder) {
            $address = $user->addresses()->first();

            if ($address) {
                Order::create([
                    'item_id' => $item->id,
                    'buyer_id' => $user->id,
                    'seller_id' => $item->seller_id,
                    'address_id' => $address->id,
                    'payment_method' => 'credit_card',
                    'status' => 'pending',
                ]);

                $item->update(['status' => 'sold']);
            }
        }

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }
}
