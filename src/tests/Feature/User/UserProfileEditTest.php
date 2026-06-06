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
            'prefecture' => '東京都千代田区 丸の内1-1-1',
            'street_address' => '',
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
        $response->assertSee('東京都千代田区 丸の内1-1-1');

        // プロフィール画像が表示されていることを確認
        $response->assertSee(asset('storage/profiles/user-avatar.jpg'));
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
            'prefecture' => '東京都千代田区 丸の内1-1-1',
            'street_address' => '',
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
            'prefecture' => '大阪府大阪市北区 1-1-1',
            'street_address' => '',
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページを取得
        $response = $this->get(route('mypage.profile'));

        // レスポンスステータスを確認
        $response->assertStatus(200);

        // 都道府県の初期値が表示されていることを確認
        $response->assertSee('大阪府大阪市北区 1-1-1');
    }
}
