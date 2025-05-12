<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 共通のセットアップ処理（CSRFミドルウェアの無効化）
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * ログイン後、マイリストページにいいねした商品が表示されるかをテストする
     */
    public function test_favorite_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $user->favorites()->attach($product->id);

        $response = $this->get('/?page=mylist&search=');
        $response->assertSee($product->name);
    }

    /**
     * ログイン後、購入済みの商品に「Sold」のラベルが表示されるかをテストする
     */
    public function test_sold_label()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->get('/?page=mylist&search=');
        $response->assertSee('Sold Out');
    }

    /**
     * ログイン後、自分が出品した商品がマイリストに表示されないかをテストする
     */
    public function test_sell_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status' => 'selling',
        ]);

        $response = $this->get('/?page=mylist&search=');
        $response->assertDontSee($product->name);
    }

    /**
     * 未ログイン状態では、マイリストページに商品が表示されないかをテストする
     */
    public function test_not_authenticated()
    {
        $response = $this->get('/?page=mylist&search=');
        $response->assertSeeText('マイリストに商品はありません');
    }
}