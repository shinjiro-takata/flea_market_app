<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 変更項目が初期値として過去設定されていること
     */
    public function test_profile_edit_form_displays_current_values_as_initial_values()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/user-avatar.jpg',
        ]);

        // ユーザーに配送先住所を登録
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザー名の初期値が表示されていることを確認
        $response->assertSee('テストユーザー');

        // 郵便番号の初期値が表示されていることを確認
        $response->assertSee('123-4567');

        // 都道府県の初期値が表示されていることを確認
        $response->assertSee('東京都');

        // 市区町村の初期値が表示されていることを確認
        $response->assertSee('千代田区');

        // 街道名の初期値が表示されていることを確認
        $response->assertSee('丸の内1-1-1');

        // プロフィール画像が表示されていることを確認
        $response->assertSee('profiles/user-avatar.jpg');
    }

    /**
     * ユーザー名の初期値が表示される
     */
    public function test_user_name_initial_value_is_displayed()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザー名の初期値が表示されていることを確認
        $response->assertSee('John Doe');
    }

    /**
     * 郵便番号の初期値が表示される
     */
    public function test_postal_code_initial_value_is_displayed()
    {
        $user = User::factory()->create();

        // ユーザーに配送先住所を登録
        Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '丸の内1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 郵便番号の初期値が表示されていることを確認
        $response->assertSee('100-0001');
    }

    /**
     * 都道府県の初期値が表示される
     */
    public function test_prefecture_initial_value_is_displayed()
    {
        $user = User::factory()->create();

        // ユーザーに配送先住所を登録
        Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '大阪府',
            'municipality' => '大阪市北区',
            'street_address' => '中之島1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 都道府県の初期値が表示されていることを確認
        $response->assertSee('大阪府');
    }

    /**
     * 市区町村の初期値が表示される
     */
    public function test_municipality_initial_value_is_displayed()
    {
        $user = User::factory()->create();

        // ユーザーに配送先住所を登録
        Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '渋谷区',
            'street_address' => '神宮前1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 市区町村の初期値が表示されていることを確認
        $response->assertSee('渋谷区');
    }

    /**
     * 街道名の初期値が表示される
     */
    public function test_street_address_initial_value_is_displayed()
    {
        $user = User::factory()->create();

        // ユーザーに配送先住所を登録
        Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'prefecture' => '東京都',
            'municipality' => '千代田区',
            'street_address' => '霞が関1-1-1',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 街道名の初期値が表示されていることを確認
        $response->assertSee('霞が関1-1-1');
    }

    /**
     * プロフィール画像が初期値として表示される
     */
    public function test_profile_image_initial_value_is_displayed()
    {
        $user = User::factory()->create([
            'profile_image' => 'profiles/test-image.jpg',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // プロフィール画像が表示されていることを確認
        $response->assertSee('profiles/test-image.jpg');
    }

    /**
     * アドレスが未設定の場合、空の値が表示される
     */
    public function test_empty_address_fields_when_no_address_is_registered()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // ユーザー名は表示される
        $response->assertSee('テストユーザー');
    }
}
