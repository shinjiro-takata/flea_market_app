<?php

namespace Tests\Feature\Purchase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Address;
use Stripe\Checkout\Session as StripeSession;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 支払い方法選択画面で支払い方法を選択できる
     */
    public function test_payment_method_options_are_displayed_in_purchase_form()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 5000,
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ユーザーにアドレス登録
        Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // 購入確認画面を取得
        $response = $this->get(route('purchase.show', $item->id));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 支払い方法のオプションが表示されていることを確認
        $response->assertSee('クレジットカード');
        $response->assertSee('コンビニ支払い');

        // ラジオボタンが表示されていることを確認
        $response->assertSee('credit_card');
        $response->assertSee('convenience_store');
    }

    /**
     * クレジットカード支払い方法で正しく反映される
     */
    public function test_credit_card_payment_method_is_selected_correctly()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ユーザーにアドレス登録
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // Stripe セッション作成をモック
        $mockSession = \Mockery::mock('overload:Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')
            ->with(\Mockery::on(function ($args) {
                // payment_method_types に 'card' が含まれていることを確認
                return isset($args['payment_method_types']) && in_array('card', $args['payment_method_types']);
            }))
            ->andReturn((object)['url' => 'https://checkout.stripe.com/pay/mock_session_id']);

        // 購入処理を実行（クレジットカード支払い）
        $response = $this->post(route('purchase.store', $item->id), [
            'address_id' => $address->id,
            'payment_method' => 'credit_card',
        ]);

        // Stripe へのリダイレクトを確認
        $response->assertRedirect();
    }

    /**
     * コンビニ支払い方法で正しく反映される
     */
    public function test_convenience_store_payment_method_is_selected_correctly()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ユーザーにアドレス登録
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // Stripe セッション作成をモック
        $mockSession = \Mockery::mock('overload:Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')
            ->with(\Mockery::on(function ($args) {
                // payment_method_types に 'konbini' が含まれていることを確認
                return isset($args['payment_method_types']) && in_array('konbini', $args['payment_method_types']);
            }))
            ->andReturn((object)['url' => 'https://checkout.stripe.com/pay/mock_session_id']);

        // 購入処理を実行（コンビニ支払い）
        $response = $this->post(route('purchase.store', $item->id), [
            'address_id' => $address->id,
            'payment_method' => 'convenience_store',
        ]);

        // Stripe へのリダイレクトを確認
        $response->assertRedirect();
    }
}
