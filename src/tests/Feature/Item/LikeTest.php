<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_toggle_like()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertJson(['liked' => true, 'likes_count' => 1]);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_toggle_unlike()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item->likes()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertJson(['liked' => false, 'likes_count' => 0]);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_like_without_authentication()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/like");

        $response->assertRedirect('/login');
    }
}
