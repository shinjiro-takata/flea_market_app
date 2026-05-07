<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\ItemImage;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録される
     */
    public function test_can_like_item_and_like_count_increases()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ログイン
        $this->actingAs($user);

        // いいね追加前のいいね数を確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねボタンをクリック
        $response = $this->post(route('like.toggle', $item->id));

        // リダイレクト確認
        $response->assertStatus(302);

        // いいねが追加されたか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品詳細ページでいいね数が増加していることを確認
        $itemDetail = Item::withCount('likes')->find($item->id);
        $this->assertEquals(1, $itemDetail->likes_count);
    }

    /**
     * 追加済みのアイコンは色が変化する
     */
    public function test_liked_icon_color_changes()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ログイン
        $this->actingAs($user);

        // いいねを事前に追加
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品詳細ページを取得
        $response = $this->get(route('items.show', $item->id));

        // ピンク色のハートアイコンが表示されていることを確認
        $response->assertSee('ハートロゴ_ピンク.png');
    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function test_can_unlike_item_and_like_count_decreases()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品',
            'status' => 'on_sale',
        ]);

        // 商品画像を直接作成
        ItemImage::create([
            'item_id' => $item->id,
            'image_path' => 'items/test.jpg',
        ]);

        // ログイン
        $this->actingAs($user);

        // いいねを事前に追加
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね削除前のいいね数を確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねボタンをクリック（解除）
        $response = $this->post(route('like.toggle', $item->id));

        // リダイレクト確認
        $response->assertStatus(302);

        // いいねが削除されたか確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品詳細ページでいいね数が減少していることを確認
        $itemDetail = Item::withCount('likes')->find($item->id);
        $this->assertEquals(0, $itemDetail->likes_count);
    }
}
