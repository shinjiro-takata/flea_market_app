<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_guest_cannot_access_sell_page()
    {
        $response = $this->get('/sell');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_sell_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);
    }
}
