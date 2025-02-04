<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class LikedProductTest extends TestCase
{
    use RefreshDatabase;

    // いいねアイコンを押下しいいねした商品として登録できるか
    public function test_user_can_like_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->postJson("/products/{$product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => true,
                ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    // いいね追加済みのアイコンは色が変化するか
    public function test_like_icon_changes_color_when_liked()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->postJson("/products/{$product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => true,
                    'favoriteCount' => 1
                ]);
    }

    // 再度いいねアイコンを押下することにより、いいねを解除できるか
    public function test_user_can_unlike_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $this->postJson("/products/{$product->id}/toggle-favorite");

        $response = $this->postJson("/products/{$product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => false,
                ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}