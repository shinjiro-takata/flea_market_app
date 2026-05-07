<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Comment;
use App\Models\Category;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テスト用のカテゴリを作成
        Category::factory()->count(10)->create();
    }

    /**
     * 必要な情報が表示される
     */
    public function test_all_required_item_information_is_displayed()
    {
        $seller = User::factory()->create();
        $commenter = User::factory()->create();

        // 商品を作成
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 5000,
            'condition' => '良好',
            'description' => 'これはテスト商品の説明です',
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // コメントを直接作成
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commenter->id,
            'comment' => 'テストコメント',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // 商品情報の確認
        $response->assertSee($item->name);
        $response->assertSee($item->brand_name);
        $response->assertSee(number_format($item->price)); // フォーマットされた価格
        $response->assertSee($item->condition);
        $response->assertSee($item->description);

        // 出品者情報の確認
        $response->assertSee($seller->name);

        // コメント情報の確認
        $response->assertSee($commenter->name);
        $response->assertSee('テストコメント');
    }

    /**
     * 複数選択されたカテゴリが表示されている
     */
    public function test_multiple_categories_are_displayed()
    {
        $seller = User::factory()->create();

        // カテゴリを取得（新しいカテゴリを作成）
        $category1 = Category::factory()->create(['name' => 'カテゴリA']);
        $category2 = Category::factory()->create(['name' => 'カテゴリB']);
        $category3 = Category::factory()->create(['name' => 'カテゴリC']);

        // 商品を作成
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'マルチカテゴリ商品',
            'status' => 'on_sale',
        ]);

        // 商品に複数のカテゴリを関連付け
        $item->categories()->attach([
            $category1->id,
            $category2->id,
            $category3->id,
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // すべてのカテゴリが表示されているか確認
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee($category3->name);
    }

    /**
     * いいね数とコメント数が表示される
     */
    public function test_likes_and_comments_count_are_displayed()
    {
        $seller = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 商品を作成
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'on_sale',
        ]);

        // いいねを追加
        $item->likes()->create(['user_id' => $user1->id]);
        $item->likes()->create(['user_id' => $user2->id]);

        // コメントを直接作成
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $user1->id,
            'comment' => 'コメント1',
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $user2->id,
            'comment' => 'コメント2',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // いいね数とコメント数が含まれているか（数値の確認）
        $response->assertViewHas('item', function ($viewItem) {
            return $viewItem->likes_count === 2 && $viewItem->comments_count === 2;
        });
    }
}
