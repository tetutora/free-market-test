<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class PurchaseProductTest extends TestCase
{
    /**
     * 商品購入のテスト（Stripeの画面表示確認）
     *
     * @return void
     */

    // 購入ボタンクリック後、stripe画面へ遷移するか
    public function test_stripe_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->get(route('products.purchase', ['item_id' => $product->id]));
        $response->assertStatus(200);

        $response = $this->post(route('products.purchase', ['item_id' => $product->id]), [
            'item_id' => $product->id,
        ]);

        $response->assertStatus(302);
        $response->assertRedirectContains('/');
    }

    // 商品購入処理完了後、Sold Outのラベルが表示されるか
    public function test_sold_label()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/');

        $response->assertSee('Sold Out');
    }

    // 商品購入後、マイページの購入した商品ページに表示されるか
    public function test_purchase_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/?page=mylist&search=');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }
}
