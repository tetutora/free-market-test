<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    // ユーザーにログイン後、マイリストページでいいねした商品が表示されるか
    public function test_favorite_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $user->favorites()->attach($product->id); // いいねをつける

        $response = $this->get('/?page=mylist&search=');

        $response->assertSee($product->name);
    }

    // ログイン後、購入済み商品に「Sold」のラベルが表示されるか
    public function test_sold_label()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $purchase = \App\Models\Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/?page=mylist&search=');

        $response->assertSee('Sold Out');
    }

    // ユーザーにログイン後、自分が出品した商品がマイリストに表示されないか
    public function test_selling_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/?page=mylist&search=');

        $response->assertDontSee($product->name);
    }

    // 未認証の場合、マイリストページで何も表示されないか
    public function test_not_authenticated()
    {
        $response = $this->get('/?page=mylist&search=');

        $response->assertSeeText('マイリストに商品はありません');
    }
}
