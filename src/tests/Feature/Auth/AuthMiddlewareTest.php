<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthMiddlewareTest extends TestCase
{
    /**
     * 未ログインユーザーは出品画面にアクセスできない
     */
    public function test_guest_cannot_access_sell_page()
    {
        $response = $this->get('/sell');
        $response->assertRedirect('/login');
    }

    /**
     * ログイン済みユーザーは出品画面にアクセスできる
     */
    public function test_authenticated_user_can_access_sell_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);
    }
}
