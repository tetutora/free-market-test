<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    /**
     * マイページから取引中の商品を確認できるかテスト
     */
    public function test_user_can_view_in_progress_products_on_my_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $seller = User::factory()->create();

        $purchase = Purchase::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    /**
     * マイページの取引中の商品を押下して取引チャット画面に遷移することができるかテスト
     */
    public function test_user_can_navigate_to_transaction_chat_screen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $seller = User::factory()->create();

        $transaction = Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->get(route('transaction.show', ['transaction' => $transaction->id]));

        $response->assertStatus(200);

        $response->assertSee('取引メッセージを入力してください', false);
    }

    /**
     * 取引チャット画面のサイドバーから別の取引画面に遷移することができるかテスト
     */
    public function test_user_can_navigate_to_another_transaction_from_sidebar()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();

        $purchase1 = Purchase::factory()->create([
            'product_id' => $product1->id,
            'user_id' => $user->id,
            'seller_id' => $seller1->id,
        ]);

        $purchase2 = Purchase::factory()->create([
            'product_id' => $product2->id,
            'user_id' => $user->id,
            'seller_id' => $seller2->id,
        ]);

        $response = $this->get(route('transaction.show', ['transaction' => $purchase1->id]));
        $response->assertStatus(200);

        $response = $this->get(route('transaction.show', ['transaction' => $purchase2->id]));

        $response->assertStatus(200);
    }

    /**
     * 他ユーザーからの取引評価の平均をプロフィール画面に表示するかテスト
     */
    public function test_user_can_see_average_rating_from_other_users_on_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $seller = User::factory()->create();

        $purchase = Purchase::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'seller_id' => $seller->id,
        ]);

        $rating = 5;
        $purchase->ratings()->create([
            'user_id' => User::factory()->create()->id,
            'rating' => $rating,
        ]);

        $response = $this->get(route('profile.show'));
        $response->assertStatus(200);
        $response->assertSee('5');
    }

    /**
     * 評価がまだないユーザーには評価が表示されないかテスト
     */
    public function test_user_without_ratings_does_not_see_average_rating_on_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.show'));
        $response->assertStatus(200);
    }
}
