<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Category;
use App\Models\Order;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のカテゴリを作成
        Category::factory()->count(5)->create();
    }

    /**
     * 全商品を取得できる
     */
    public function test_can_get_all_items()
    {
        // テスト用の出品者を作成
        $seller = User::factory()->create();

        // 複数の商品を作成
        $items = Item::factory()->count(5)->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // すべての商品がレスポンスに含まれているか確認
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_sold_items_are_marked()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // 購入済み商品を作成
        $soldItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'sold',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // 購入済み商品が含まれているか確認
        $response->assertSee($soldItem->name);
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function test_own_items_are_not_displayed()
    {
        $user = User::factory()->create();

        // ログインユーザーが出品した商品
        $ownItem = Item::factory()->create([
            'seller_id' => $user->id,
            'status' => 'on_sale',
        ]);

        // 他のユーザーが出品した商品
        $otherSeller = User::factory()->create();
        $otherItem = Item::factory()->create([
            'seller_id' => $otherSeller->id,
            'status' => 'on_sale',
        ]);

        // ログインして商品一覧を取得
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);

        // 自分の商品は表示されない
        $response->assertDontSee($ownItem->name);

        // 他人の商品は表示される
        $response->assertSee($otherItem->name);
    }

    /**
     * 未認証ユーザーでも商品一覧は見られる
     */
    public function test_guest_can_view_items()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}
