<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExhibitionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_create_item()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'Test Item',
            'description' => 'This is a test item.',
            'price' => 1000,
            'condition' => '良好',
            'category' => 1,
            'brand_name' => 'TestBrand',
            'image' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'price' => 1000,
            'brand_name' => 'TestBrand',
        ]);
    }
}
