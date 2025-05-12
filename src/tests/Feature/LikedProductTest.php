<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikedProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::factory()->create();
    }

    /**
     * いいねアイコンを押下し、いいねした商品として登録できるかをテストする
     */
    public function test_like_product()
    {
        $response = $this->postJson("/products/{$this->product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => true,
                ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    /**
     * いいね追加済みのアイコンが色を変える（レスポンスに favoriteCount を含む）ことをテストする
     */
    public function test_changes_color()
    {
        $response = $this->postJson("/products/{$this->product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => true,
                    'favoriteCount' => 1,
                ]);
    }

    /**
     * 再度いいねアイコンを押下することで、いいねを解除できるかをテストする
     */
    public function test_unlike_product()
    {
        $this->postJson("/products/{$this->product->id}/toggle-favorite");

        $response = $this->postJson("/products/{$this->product->id}/toggle-favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'favorited' => false,
                ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }
}
