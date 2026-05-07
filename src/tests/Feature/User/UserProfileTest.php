<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Order;
use App\Models\Address;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_user_profile_displays_all_required_information()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/test.jpg',
        ]);

        $otherUser = User::factory()->create();

        // ユーザーが出品した商品を作成
        $sellItem1 = Item::factory()->create([
            'seller_id' => $user->id,
            'name' => '出品商品1',
            'status' => 'on_sale',
        ]);

        $sellItem2 = Item::factory()->create([
            'seller_id' => $user->id,
            'name' => '出品商品2',
            'status' => 'sold',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $sellItem1->id,
            'image_path' => 'items/sell1.jpg',
        ]);

        ItemImage::create([
            'item_id' => $sellItem2->id,
            'image_path' => 'items/sell2.jpg',
        ]);

        // ユーザーが購入した商品を作成
        $buyItem1 = Item::factory()->create([
            'seller_id' => $otherUser->id,
            'name' => '購入商品1',
            'status' => 'sold',
        ]);

        $buyItem2 = Item::factory()->create([
            'seller_id' => $otherUser->id,
            'name' => '購入商品2',
            'status' => 'sold',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $buyItem1->id,
            'image_path' => 'items/buy1.jpg',
        ]);

        ItemImage::create([
            'item_id' => $buyItem2->id,
            'image_path' => 'items/buy2.jpg',
        ]);

        // 配送先を登録
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // 購入記録を作成
        Order::create([
            'item_id' => $buyItem1->id,
            'buyer_id' => $user->id,
            'seller_id' => $otherUser->id,
            'address_id' => $address->id,
            'payment_method' => 'credit_card',
            'status' => 'pending',
        ]);

        Order::create([
            'item_id' => $buyItem2->id,
            'buyer_id' => $user->id,
            'seller_id' => $otherUser->id,
            'address_id' => $address->id,
            'payment_method' => 'credit_card',
            'status' => 'pending',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィールページを取得
        $response = $this->get(route('items.mypage'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザー名が表示されていることを確認
        $response->assertSee('テストユーザー');

        // 出品した商品が表示されていることを確認
        $response->assertSee('出品商品1');
        $response->assertSee('出品商品2');

        // 購入した商品が表示されていることを確認
        $response->assertSee('購入商品1');
        $response->assertSee('購入商品2');

        // ユーザーの出品商品数を確認
        $this->assertEquals(2, $user->items()->count());

        // ユーザーの購入商品数を確認
        $this->assertEquals(2, $user->purchaseOrders()->count());
    }

    /**
     * プロフィール画像が取得できる
     */
    public function test_user_profile_image_is_displayed()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/user-avatar.jpg',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィールページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // プロフィール画像が表示されていることを確認
        $response->assertSee('profiles/user-avatar.jpg');
    }

    /**
     * 出品した商品がない場合、出品商品一覧が空で表示される
     */
    public function test_empty_sell_items_list_when_user_has_no_items()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        // ログイン
        $this->actingAs($user);

        // マイページを取得
        $response = $this->get(route('items.mypage'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザーの出品商品がないことを確認
        $this->assertEquals(0, $user->items()->count());
    }

    /**
     * 購入した商品がない場合、購入商品一覧が空で表示される
     */
    public function test_empty_purchase_items_list_when_user_has_no_purchases()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        // ログイン
        $this->actingAs($user);

        // マイページを取得
        $response = $this->get(route('items.mypage'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザーの購入商品がないことを確認
        $this->assertEquals(0, $user->purchaseOrders()->count());
    }
}
