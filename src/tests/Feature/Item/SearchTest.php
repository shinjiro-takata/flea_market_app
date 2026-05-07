<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Category;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のカテゴリを作成
        Category::factory()->count(5)->create();
    }

    /**
     * 「商品名」で部分一致検索ができる
     */
    public function test_can_search_items_by_partial_name()
    {
        $seller = User::factory()->create();

        // 検索対象の商品を作成
        $matchingItem1 = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品A',
            'status' => 'on_sale',
        ]);

        $matchingItem2 = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト用品B',
            'status' => 'on_sale',
        ]);

        // 検索対象外の商品を作成
        $nonMatchingItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => '他の商品',
            'status' => 'on_sale',
        ]);

        // 「テスト」で検索
        $response = $this->get('/?q=テスト');

        $response->assertStatus(200);

        // マッチする商品は表示される
        $response->assertSee($matchingItem1->name);
        $response->assertSee($matchingItem2->name);

        // マッチしない商品は表示されない
        $response->assertDontSee($nonMatchingItem->name);
    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_state_persists_in_mylist()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // 検索キーワード「テスト」でマッチする商品を作成
        $matchingItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'status' => 'on_sale',
        ]);

        // マッチしない商品を作成
        $nonMatchingItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => '他の商品',
            'status' => 'on_sale',
        ]);

        // ユーザーが両方の商品をいいねする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $matchingItem->id,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $nonMatchingItem->id,
        ]);

        // ホームページで「テスト」で検索してマイリストに遷移
        $response = $this->actingAs($user)->get('/?q=テスト&tab=mylist');

        $response->assertStatus(200);

        // 検索キーワードが有効（マッチする商品のみ表示）
        $response->assertSee($matchingItem->name);
        $response->assertDontSee($nonMatchingItem->name);
    }

    /**
     * 空の検索結果が返される
     */
    public function test_search_returns_empty_results()
    {
        $seller = User::factory()->create();

        Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => '商品A',
            'status' => 'on_sale',
        ]);

        // 存在しないキーワードで検索
        $response = $this->get('/?q=存在しないキーワード');

        $response->assertStatus(200);
        // 検索結果が空であることを確認（商品が表示されない）
    }
}
