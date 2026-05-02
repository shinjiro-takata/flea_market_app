<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_item()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categoryIds = Category::take(2)->pluck('id')->toArray();

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'Test Item',
            'description' => 'This is a test item.',
            'price' => 1000,
            'condition' => '良好',
            'categories' => $categoryIds,
            'brand_name' => 'TestBrand',
            'image' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect();
    }

    public function test_item_with_categories()
    {
        Storage::fake('public');

        // カテゴリを作成
        Category::factory()->count(3)->create();

        $user = User::factory()->create();
        $this->actingAs($user);

        $categoryIds = Category::take(2)->pluck('id')->toArray();

        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'description' => 'テスト説明',
            'image' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
            'brand_name' => 'テストブランド',
            'condition' => '良好',
            'price' => 1000,
            'categories' => $categoryIds,
        ]);

        $response->assertRedirect();

        $item = Item::latest()->first();
        $this->assertNotNull($item);
        $this->assertEquals($item->categories()->count(), 2);
    }
}
