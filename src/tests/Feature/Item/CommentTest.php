<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_send_comment()
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

        // コメント送信前のコメント数を確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // コメントを送信
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => 'これは素晴らしい商品です！',
        ]);

        // リダイレクト確認
        $response->assertStatus(302);

        // コメントが保存されたか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これは素晴らしい商品です！',
        ]);

        // コメント数が増加していることを確認
        $itemDetail = Item::withCount('comments')->find($item->id);
        $this->assertEquals(1, $itemDetail->comments_count);
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_unauthenticated_user_cannot_send_comment()
    {
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

        // ログインせずにコメント送信を試行
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => 'これは素晴らしい商品です！',
        ]);

        // ログインページへリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'これは素晴らしい商品です！',
        ]);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_required_validation()
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

        // 空のコメントを送信
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => '',
        ]);

        // バリデーションエラーが発生したことを確認
        $response->assertSessionHasErrors('comment');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length_validation()
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

        // 256字のコメントを送信
        $longComment = str_repeat('あ', 256);
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => $longComment,
        ]);

        // バリデーションエラーが発生したことを確認
        $response->assertSessionHasErrors('comment');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
