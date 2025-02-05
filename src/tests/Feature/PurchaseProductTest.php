<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

class PurchaseProductTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // ユーザーが商品を購入できるか
    public function test_purchase_product()
    {
        $user = User::factory()->create();
        $user->email_verified_at = now();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'is_sold' => false
        ]);

        $response = $this->post("/purchase/{$product->id}/success");

        $response->assertStatus(302);
    }

    // 購入した商品が商品一覧で「Sold Out」と表示されるか
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

    // 購入した商品がプロフィールの購入した商品一覧に追加されているか
    public function test_selling_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'user_id' => $user->id,
            'is_sold' => false
        ]);

        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/?page=mylist&search=');

        $response->assertSee($product->name);
    }
}
