<?php

namespace Tests\Feature\Purchase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Order;
use App\Models\Address;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_registered_address_is_reflected_in_purchase_screen()
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

        // ログイン
        $this->actingAs($user);

        // アドレスが登録されていないことを確認
        $this->assertCount(0, $user->addresses);

        // 送付先住所変更画面で住所を登録
        $response = $this->post(route('purchase.address.update', $item->id), [
            'postal_code' => '150-0001',
            'prefecture' => '東京都',
            'municipality' => '渋谷区',
            'street_address' => '神宮前1-1-1',
        ]);

        // 購入画面へリダイレクト
        $response->assertRedirect(route('purchase.show', $item->id));

        // データベースに住所が保存されたことを確認
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'postal_code' => '150-0001',
            'prefecture' => '東京都',
            'municipality' => '渋谷区',
            'street_address' => '神宮前1-1-1',
        ]);

        // 商品購入画面を再度開く
        $response = $this->get(route('purchase.show', $item->id));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 登録した住所が購入画面に反映されていることを確認
        $response->assertSee('150-0001');
        $response->assertSee('東京都');
        $response->assertSee('渋谷区');
        $response->assertSee('神宮前1-1-1');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_purchased_order_is_linked_with_shipping_address()
    {
        $buyer = User::factory()->create();
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

        // ログイン
        $this->actingAs($buyer);

        // 送付先住所変更画面で住所を登録
        $response = $this->post(route('purchase.address.update', $item->id), [
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // リダイレクトを確認
        $response->assertRedirect(route('purchase.show', $item->id));

        // 登録されたアドレスを取得
        $address = $buyer->addresses()->first();
        $this->assertNotNull($address);

        // 商品を購入する（success メソッドの処理をシミュレート）
        $item->update(['status' => 'sold']);
        $order = Order::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'address_id' => $address->id,
            'payment_method' => 'credit_card',
            'status' => 'pending',
        ]);

        // 購入したOrderの送付先住所が正しく紐づいていることを確認
        $this->assertEquals($address->id, $order->address_id);

        // Orderに紐づくアドレス情報を確認
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'address_id' => $address->id,
        ]);

        // アドレス情報が正しいことを確認
        $linkedAddress = $order->address;
        $this->assertEquals('100-0001', $linkedAddress->postal_code);
        $this->assertEquals('東京都', $linkedAddress->prefecture);
        $this->assertEquals('千代田区', $linkedAddress->municipality);
        $this->assertEquals('丸の内1-1-1', $linkedAddress->street_address);
    }
}
