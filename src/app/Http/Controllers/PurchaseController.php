<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPaymentRequest;
use App\Models\Address;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    private const DEFAULT_PAYMENT_METHOD = 'convenience_store';

    /**
     * 購入画面を表示する
     */
    public function show($item_id)
    {
        $item = Item::find($item_id);
        $address = auth()->user()->addresses()->first();

        // アドレスが登録されていない場合はプロフィール画面へ
        if (!$address) {
            return redirect()->route('mypage.profile')->with('error', '配送先を登録してください');
        }

        $paymentMethod = session('payment_method', self::DEFAULT_PAYMENT_METHOD);

        return view('purchase.show', [
            'item' => $item,
            'address' => $address,
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * 支払い方法をセッションに保存する
     */
    public function setPayment(SetPaymentRequest $request, $item_id)
    {
        $validated = $request->validated();

        session(['payment_method' => $validated['payment_method']]);

        return redirect()->route('purchase.show', $item_id);
    }

    /**
     * Stripe の決済セッションを作成して遷移する
     */
    public function store(Request $request, $item_id)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $this->initializeStripe();

        $item = Item::find($item_id);
        $address = Address::find($validated['address_id']);
        $paymentMethod = session('payment_method', self::DEFAULT_PAYMENT_METHOD);

        if (!$item || !$address) {
            return redirect()->route('items.index')->with('error', '商品または住所が見つかりません');
        }

        try {
            $session = StripeSession::create($this->buildStripeSessionData(
                $item,
                $address,
                $paymentMethod
            ));

            return redirect($session->url);
        } catch (\Exception $e) {
            \Log::error('Stripe session error: ' . $e->getMessage());
            return redirect()->route('items.index')->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * Stripe API キーを設定する
     */
    private function initializeStripe()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Stripe セッション用のリクエストデータを組み立てる
     */
    private function buildStripeSessionData($item, $address, $paymentMethod)
    {
        return [
            'payment_method_types' => $this->getPaymentMethodTypes($paymentMethod),
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                        'images' => $this->getItemImageUrl($item),
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
        ];
    }

    /**
     * 支払い方法から Stripe の決済タイプを決める
     */
    private function getPaymentMethodTypes($paymentMethod)
    {
        return match ($paymentMethod) {
            'credit_card' => ['card'],
            'convenience_store' => ['konbini'],
            default => ['card'],
        };
    }

    /**
     * Stripe に渡す商品画像 URL を取得する
     */
    private function getItemImageUrl($item)
    {
        if ($item->images->isEmpty()) {
            return [];
        }

        $imagePath = $item->images->first()->image_path;
        $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
            ? $imagePath
            : asset('storage/' . $imagePath);

        return [$imageUrl];
    }

    /**
     * 決済成功後に注文を確定する
     */
    public function success($item_id)
    {
        $this->initializeStripe();

        $item = Item::find($item_id);
        $user = auth()->user();

        if (!$item) {
            return redirect()->route('items.index')->with('error', '商品が見つかりません');
        }

        // 既に注文が作成済みかを確認（キャンセル済みを除外）
        $existingOrder = Order::where('item_id', $item->id)
            ->where('buyer_id', $user->id)
            ->whereNot('status', 'cancelled')
            ->first();

        if (!$existingOrder) {
            $address = $user->addresses()->first();

            if ($address) {
                Order::create([
                    'item_id' => $item->id,
                    'buyer_id' => $user->id,
                    'seller_id' => $item->seller_id,
                    'address_id' => $address->id,
                    'payment_method' => session('payment_method', self::DEFAULT_PAYMENT_METHOD),
                    'status' => 'pending',
                ]);

                $item->update(['status' => 'sold']);
            }
        }

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }
}
