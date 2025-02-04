<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Purchase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 全商品を取得できるか
    public function testAllProductsAreDisplayed()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertSee($product2->name);
    }

    // 購入済み商品は「Sold Out」と表示されるか
    public function testSoldLabelForPurchasedProducts()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $purchase = Purchase::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($product->isSold());

        $response = $this->get('/products');

        $response->assertSee($product->name);
        $response->assertSee('Sold Out');
    }

    // 自分が出品した商品は商品一覧に表示されないか
    public function testUserCannotSeeTheirOwnProductInProductList()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee($product->name);
    }
}
