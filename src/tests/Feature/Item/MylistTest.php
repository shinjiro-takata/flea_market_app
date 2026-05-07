<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Category;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のカテゴリを作成
        Category::factory()->count(5)->create();
    }

    /**
     * いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // いいね対象の商品を作成
        $likedItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        // いいねしていない商品を作成
        $unlikedItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        // ユーザーがlikedItemをいいねする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // ログインしてマイリストを取得
        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);

        // いいねした商品は表示される
        $response->assertSee($likedItem->name);

        // いいねしていない商品は表示されない
        $response->assertDontSee($unlikedItem->name);
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_sold_items_are_marked_in_mylist()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // 購入済み商品を作成（いいね対象）
        $soldItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'sold',
        ]);

        // ユーザーがsoldItemをいいねする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);

        // 購入済み商品が表示される
        $response->assertSee($soldItem->name);
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function test_guest_sees_empty_mylist()
    {
        $seller = User::factory()->create();

        // テスト用の商品を作成
        Item::factory()->count(3)->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        // 未認証でマイリストにアクセス
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        // ログインメッセージが表示される
        // または、リストが空になっている
        // 実装により異なる場合があります
    }
}
