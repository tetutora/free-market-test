<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品を作成する
     */
    protected function createProduct(int $userId)
    {
        return Product::factory()->create([
            'user_id' => $userId,
        ]);
    }

    /**
     * 全商品を取得できるか
     */
    public function test_all_products_show()
    {
        $product1 = $this->createProduct(User::factory()->create()->id);
        $product2 = $this->createProduct(User::factory()->create()->id);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertSee($product2->name);
    }

    /**
     * 購入済み商品は「Sold Out」と表示されるか
     */
    public function test_sold_label_products()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $product = $this->createProduct($seller->id);

        $purchase = Purchase::factory()->create([
            'product_id' => $product->id,
            'user_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->assertTrue($product->isSold());

        $response = $this->get('/products');

        $response->assertSee($product->name);
        $response->assertSee('Sold Out');
    }

    /**
     * 自分が出品した商品は商品一覧に表示されないか
     */
    public function test_cannot_see_own_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'name' => '自分の商品',
            'user_id' => $user->id,
        ]);

        Product::factory()->create([
            'name' => '他人の商品',
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}