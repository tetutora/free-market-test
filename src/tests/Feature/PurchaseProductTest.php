<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー、商品、購入を作成する
     */
    protected function createUserProductPurchase(array $userData, array $productData)
    {
        $user = User::factory()->create($userData);
        $seller = User::factory()->create();
        $product = Product::factory()->create(array_merge($productData, ['user_id' => $seller->id]));

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'seller_id' => $seller->id,
        ]);

        return compact('user', 'product', 'purchase');
    }

    /**
     * 購入ボタンクリック後、stripe画面へ遷移するか
     */
    public function test_stripe_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->get(route('products.purchase', ['item_id' => $product->id]));
        $response->assertStatus(200);

        $response = $this->post(route('products.purchase', ['item_id' => $product->id]), [
            'item_id' => $product->id,
        ], [
            'X-CSRF-TOKEN' => csrf_token(),
        ]);

        $response->assertStatus(302);
        $response->assertRedirectContains('/');
    }

    /**
     * 商品購入処理完了後、Sold Outのラベルが表示されるか
     */
    public function test_sold_label()
    {
        $data = $this->createUserProductPurchase(
            ['name' => 'Buyer'],
            ['name' => 'Sample Product']
        );

        $response = $this->get('/');
        $response->assertSee('Sold Out');
    }

    /**
     * 商品購入後、マイページの購入した商品ページに表示されるか
     */
    public function test_purchase_products()
    {
        $data = $this->createUserProductPurchase(
            ['name' => 'Buyer'],
            ['name' => 'Sample Product']
        );

        $response = $this->get('/?page=mylist&search=');
        $response->assertStatus(200);
        $response->assertSee($data['product']->name);
    }
}