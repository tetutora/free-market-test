<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     *
     * @return void
     */

    // 送付先住所変更が商品購入画面に反映されるか
    public function test_address_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'zipcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => '青山マンション101号',
        ]);

        $response = $this->get(route('purchase.show', ['item_id' => $product->id]));

        $response->assertSee('〒 123-4567');
        $response->assertSee('東京都渋谷区 青山マンション101号');
    }

    /**
     *
     *
     * @return void
     */
    use RefreshDatabase;
    // 購入した商品がユーザーに紐づくか
    public function test_purchased_item_is_linked_to_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'zipcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => '青山マンション101号',
        ]);

        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'seller_id' => $product->user_id,
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}

