<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

class PurchaseProductTest extends TestCase
{
    use RefreshDatabase;

    // ユーザーが商品を購入できるか
    public function test_user_can_complete_purchase()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create();
        $product = Product::factory()->create();  // 購入対象商品を作成
        $this->actingAs($user);

        // 2. 商品購入画面を開く
        $response = $this->get(route('purchase.show', ['item_id' => $product->id]));

        // 3. 商品を選択して「購入する」ボタンを押下
        $response = $this->post(route('products.purchase', ['item_id' => $product->id]), [
            'session_id' => 'valid-session-id',  // 仮のセッションID
        ]);

        // 購入完了
        $response->assertRedirect(route('profile.mypage'));

        // 購入後の商品が「sold」として表示されている
        $product->refresh();
        $this->assertTrue($product->is_sold);
    }
}
