<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_item_can_be_created_with_all_required_information()
    {
        // カテゴリを複数作成
        $categories = Category::factory()->count(3)->create();

        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        // ファイルのアップロードをシミュレート
        $file = UploadedFile::fake()->create('test-item.jpg', 100, 'image/jpeg');

        // 商品を出品
        $response = $this->post(route('exhibition.store'), [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です',
            'price' => 5000,
            'condition' => '未使用',
            'image' => $file,
            'categories' => [$categories[0]->id, $categories[1]->id],
        ]);

        // リダイレクト確認
        $response->assertRedirect();

        // データベースに保存されたことを確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です',
            'price' => 5000,
            'condition' => '未使用',
            'status' => 'on_sale',
        ]);

        // Item を取得
        $item = Item::where('name', 'テスト商品')->first();
        $this->assertNotNull($item);

        // 商品画像が保存されたことを確認
        $this->assertDatabaseHas('item_images', [
            'item_id' => $item->id,
        ]);

        // カテゴリが正しく紐付いていることを確認
        $this->assertEquals(2, $item->categories()->count());
        $this->assertContains($categories[0]->id, $item->categories()->pluck('id')->toArray());
        $this->assertContains($categories[1]->id, $item->categories()->pluck('id')->toArray());
    }

    /**
     * 商品名が正しく保存される
     */
    public function test_item_name_is_saved_correctly()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '高級時計',
            'brand_name' => 'ROLEX',
            'description' => '説明文',
            'price' => 1000000,
            'condition' => '良好',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // 商品名が保存されたことを確認
        $this->assertDatabaseHas('items', [
            'name' => '高級時計',
        ]);
    }

    /**
     * ブランド名が正しく保存される
     */
    public function test_brand_name_is_saved_correctly()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '時計',
            'brand_name' => 'Apple Watch',
            'description' => '説明文',
            'price' => 50000,
            'condition' => '新品',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // ブランド名が保存されたことを確認
        $this->assertDatabaseHas('items', [
            'brand_name' => 'Apple Watch',
        ]);
    }

    /**
     * 商品の説明が正しく保存される
     */
    public function test_description_is_saved_correctly()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $description = '新品未使用です。とても美しい商品です。';

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '商品',
            'brand_name' => 'ブランド',
            'description' => $description,
            'price' => 5000,
            'condition' => '未使用',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // 説明が保存されたことを確認
        $this->assertDatabaseHas('items', [
            'description' => $description,
        ]);
    }

    /**
     * 販売価格が正しく保存される
     */
    public function test_price_is_saved_correctly()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '商品',
            'brand_name' => 'ブランド',
            'description' => '説明',
            'price' => 99999,
            'condition' => '未使用',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // 価格が保存されたことを確認
        $this->assertDatabaseHas('items', [
            'price' => 99999,
        ]);
    }

    /**
     * 商品の状態が正しく保存される
     */
    public function test_condition_is_saved_correctly()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '商品',
            'brand_name' => 'ブランド',
            'description' => '説明',
            'price' => 5000,
            'condition' => '傷や汚れあり',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // 状態が保存されたことを確認
        $this->assertDatabaseHas('items', [
            'condition' => '傷や汚れあり',
        ]);
    }

    /**
     * カテゴリが正しく保存される
     */
    public function test_categories_are_saved_correctly()
    {
        $categories = Category::factory()->count(5)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $selectedCategories = [$categories[0]->id, $categories[1]->id, $categories[2]->id];

        // 商品を出品
        $this->post(route('exhibition.store'), [
            'name' => '商品',
            'brand_name' => 'ブランド',
            'description' => '説明',
            'price' => 5000,
            'condition' => '未使用',
            'image' => $file,
            'categories' => $selectedCategories,
        ]);

        // Item を取得
        $item = Item::where('name', '商品')->first();
        $this->assertNotNull($item);

        // カテゴリが正しく紐付いていることを確認
        $this->assertEquals(3, $item->categories()->count());
        foreach ($selectedCategories as $categoryId) {
            $this->assertContains($categoryId, $item->categories()->pluck('id')->toArray());
        }
    }

    /**
     * 出品後、商品詳細ページへリダイレクトされる
     */
    public function test_redirects_to_item_detail_page_after_exhibition()
    {
        $categories = Category::factory()->count(1)->create();
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        // 商品を出品
        $response = $this->post(route('exhibition.store'), [
            'name' => '商品',
            'brand_name' => 'ブランド',
            'description' => '説明',
            'price' => 5000,
            'condition' => '未使用',
            'image' => $file,
            'categories' => [$categories[0]->id],
        ]);

        // Item を取得
        $item = Item::where('name', '商品')->first();

        // 商品詳細ページへリダイレクトされていることを確認
        $response->assertRedirect(route('items.show', $item->id));
    }
}
